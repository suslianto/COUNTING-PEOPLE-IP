<?php
header("Content-Type: application/json");
error_reporting(E_ALL);

class DB {
    private $conn;

    public function __construct($host, $base, $user, $pwd) {
        $this->conn = pg_connect("host=$host port=5442 dbname=$base user=$user password=$pwd");
        if (!$this->conn) {
            die(json_encode(['error' => 'Database connection failed']));
        }
    }

    public function query($sql) {
        return pg_query($this->conn, $sql);
    }

    public function fetchRow($result) {
        return pg_fetch_row($result);
    }
}

class TransactionProcessor {
    private $db;
    private $departments = [];
    private $carNumbers = [];

    public function __construct(DB $db) {
        $this->db = $db;
        $this->loadDepartments();
        $this->loadCarNumbers();
    }

    private function loadDepartments() {
        $res = $this->db->query("SELECT * FROM auth_department");
        while ($row = $this->db->fetchRow($res)) {
            $this->departments[] = $row[15];
        }
    }

    private function loadCarNumbers() {
        $res = $this->db->query("SELECT * FROM park_car_number");
        while ($row = $this->db->fetchRow($res)) {
            $this->carNumbers[] = ["id" => $row[17], "plat" => $row[14]];
        }
    }

    private function fetchPageData($page) {
        $startDate = date('y-m-d');
        $endDate = date('y-m-d', strtotime("+1 day", strtotime($startDate)));
        $url = "https://192.168.1.50:8098/api/transaction/list?endDate=20{$endDate}%2023%3A59%3A59&pageNo=$page&pageSize=800&startDate=20{$startDate}%2000%3A00%3A00&access_token=97F30B9A7890AFBD347C53322B0C5653";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ['Content-Type:application/json', 'Accept:application/json'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data["data"] ?? [];
    }

    private function processPage($pageData) {
        $full = [];
        $departments = [];

        foreach ($pageData as $val) {
            $departments[] = $val["deptName"] ?? '';
            $full[] = [
                "dept" => $val["deptName"] ?? '',
                "sn" => $val["devSn"] ?? '',
                "name" => $val["devName"] ?? '',
                "acc" => $val['eventName'] ?? '',
                "person" => $val['name'] ?? '',
                "id" => $val["pin"] ?? '',
                "time" => $val["eventTime"] ?? '',
            ];
        }

        $departments = array_filter(array_unique($departments));
        $result = [];

        foreach ($departments as $dept) {
            if ($dept === '') continue;
            $in = $out = 0;
            $inkom = $outkom = [];
            $perso = ['data' => []];

            foreach ($full as $res) {
                if (in_array($res['acc'], ['Global Anti-Passback(logical)', 'Disconnected'])) continue;
                if ($res["dept"] !== $dept) continue;

                $pin = ["TRIPOD-IN-POS-1", "MOTOR-IN-POS-1", "MOBIL-IN-1-POS-1", "MOBIL-IN-2-POS-1"];
                $pout = ["TRIPOD-OUT-POS-1", "MOTOR-OUT-POS-1", "MOBIL-OUT-1-POS-1", "MOBIL-OUT-2-POS-1"];

                foreach ($pin as $pattern) {
                    if (preg_match("/$pattern/i", $res["name"])) {
                        $in++;
                        $inkom[] = $res;
                        continue 2;
                    }
                }

                foreach ($pout as $pattern) {
                    if (preg_match("/$pattern/i", $res["name"])) {
                        $out++;
                        $outkom[] = $res;
                        continue 2;
                    }
                }
            }

            foreach ($inkom as $kom) {
                $found = false;
                foreach ($outkom as $outk) {
                    if ($kom['id'] === $outk['id']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found && $kom["dept"] === $dept) {
                    $perso['data'][] = $kom;
                }
            }

            $cur = $in - $out;
            $result["data"][] = [
                "dept" => $dept,
                "in" => $in,
                "out" => $out,
                "cur" => $cur,
                "inn" => 0,
                "outt" => 0,
                "curr" => 0,
                "tot" => count($departments) - 1,
                "person" => $perso,
            ];
        }

        return $result;
    }

    public function run() {
        $allData = [];
        $totalIn = $totalOut = $totalCur = $totalTot = 0;
        $page = 0;

        while (true) {
            $page++;
            $pageData = $this->fetchPageData($page);
            if (empty($pageData)) break;

            $processed = $this->processPage($pageData);
            foreach ($processed["data"] as $val) {
                $totalIn += $val["in"];
                $totalOut += $val["out"];
                $totalCur += $val["cur"];
                if ($totalTot === 0) $totalTot = $val["tot"];
                $allData[] = $val;
            }
        }

        $result = ['data' => []];
        foreach ($this->departments as $dept) {
            if ($dept === '') continue;

            $masuk = $keluar = $sisa = $masuk1 = $keluar1 = $sisa1 = 0;
            $tota = 0;
            $person = ['data' => []];

            foreach ($allData as $val) {
                if ($val["dept"] !== $dept) continue;

                $masuk += $val["in"];
                $keluar += $val["out"];
                $sisa += $val["cur"];
                $masuk1 += $val["inn"];
                $keluar1 += $val["outt"];
                $sisa1 += $val["curr"];
                if ($tota === 0) $tota = $val["tot"];

                foreach ($val["person"]["data"] as $kom) {
                    if ($kom["dept"] !== $dept) continue;

                    $id = pg_escape_string($kom["id"]);
                    $res = pg_fetch_row($this->db->query("SELECT * FROM pers_person WHERE pin = '$id'"));
                    if (!$res) continue;

                    $re = pg_fetch_row($this->db->query("SELECT * FROM pers_attribute_ext WHERE person_id = '{$res[0]}'"));
                    $ress = pg_fetch_row($this->db->query("SELECT * FROM park_person WHERE pers_person_pin = '$id'"));

                    $carplat = '';
                    if ($ress) {
                        foreach ($this->carNumbers as $row) {
                            if ($row["id"] == $ress[0]) {
                                $carplat = $row["plat"];
                                break;
                            }
                        }
                    }

                    $gender = $res[19] === 'M' ? 'Male' : ($res[19] === 'F' ? 'Female' : '');
                    $person['data'][] = [
                        "name" => $kom["name"],
                        "id" => $id,
                        "time" => $kom["time"],
                        "gender" => $gender,
                        "email" => $res[16] ?? '',
                        "phone" => $res[26] ?? '',
                        "plat" => $carplat,
                        "birthday" => $res[13] ?? '',
                        "nipeg" => $re[19] ?? '',
                    ];
                }
            }

            if ($sisa <= 0) {
                $person = ['data' => []];
            }

            $result["data"][] = [
                "dept" => $dept,
                "in" => $masuk,
                "out" => $keluar,
                "cur" => $sisa,
                "inn" => $masuk1,
                "outt" => $keluar1,
                "curr" => $sisa1,
                "tot" => $tota,
                "totalin" => $totalIn,
                "totalout" => $totalOut,
                "totalcur" => $totalCur,
                "totaldb" => count($this->departments) - 1,
                "person" => $person,
            ];
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}

// Eksekusi
$db = new DB("127.0.0.1", "biosecurity-boot", "root", "ZKTeco##123");
$processor = new TransactionProcessor($db);
$processor->run();
