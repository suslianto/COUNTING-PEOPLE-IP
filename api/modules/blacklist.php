<?php
header("Content-Type: application/json");

class Database {
    private $conn;

    public function __construct($host, $port, $dbname, $user, $password) {
        $connString = "host=$host port=$port dbname=$dbname user=$user password=$password";
        $this->conn = pg_connect($connString);
        if (!$this->conn) {
            throw new Exception("Failed to connect to database: " . pg_last_error());
        }
    }

    public function query($sql) {
        $result = pg_query($this->conn, $sql);
        if (!$result) {
            throw new Exception("Query error: " . pg_last_error($this->conn));
        }
        return $result;
    }

    public function fetchRow($result) {
        return pg_fetch_row($result);
    }

    public function freeResult($result) {
        pg_free_result($result);
    }

    public function close() {
        pg_close($this->conn);
    }
}

class BlacklistFetcher {
    private $db;
    private $arrContextOptions;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
    }

    public function getBlacklist() {
        $black = ['data' => []];

        $accQuery = "SELECT * FROM acc_person WHERE disabled = 't'";
        $accResult = $this->db->query($accQuery);

        while ($row = $this->db->fetchRow($accResult)) {
            $id = $row[17];

            $personQuery = "SELECT * FROM pers_person WHERE id = '$id'";
            $personResult = $this->db->query($personQuery);
            $person = $this->db->fetchRow($personResult);

            $attrQuery = "SELECT * FROM pers_attribute_ext WHERE person_id = '{$person[0]}'";
            $attrResult = $this->db->query($attrQuery);
            $attribute = $this->db->fetchRow($attrResult);
            // print_r($person);

            /*
            $photoUrl = "https://192.168.1.50:8098/accTransaction.do?getDecryptPhotoBase64&photoPath=" . $person[33] . "&access_token=416012CD899F5A14E96E09F08AF0D264";
            $photoData = file_get_contents($photoUrl, false, stream_context_create($this->arrContextOptions));
            $photoDecoded = json_decode($photoData, true);
            */

            $attQuery = "SELECT * FROM att_person WHERE pers_person_pin = '{$person[34]}'";
            $attResult = $this->db->query($attQuery);
            $attendance = $this->db->fetchRow($attResult);

            $gender = ($person[20] === "M") ? "Male" : "Female";

            $nipeg = ($attribute[19] === "") ? '' : $attribute[19];

            $black['data'][] = [
                "site" => "PT. PLN Indonesia Power Barru",
                "dept" => $person[26],
                "foto" => "",
                "name" => $person[28],
                "time" => $gender,
                "id" => $person[30],
                "nipeg" => $nipeg,
            ];

            // Free individual results
            $this->db->freeResult($personResult);
            $this->db->freeResult($attrResult);
            $this->db->freeResult($attResult);
        }

        $this->db->freeResult($accResult);

        return $black;
    }
}

try {
    $db = new Database("127.0.0.1", 5442, "biosecurity-boot", "root", "ZKTeco##123");
    $fetcher = new BlacklistFetcher($db);
    $result = $fetcher->getBlacklist();

    echo json_encode($result, JSON_PRETTY_PRINT);

    $db->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
