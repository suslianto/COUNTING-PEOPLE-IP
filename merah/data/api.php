<?php
error_reporting(0);

function coun($page) {
    date_default_timezone_set("Asia/Jakarta");

    $startDate = date('y-m-d');
    $endDate = date('y-m-d', strtotime("+1 day", strtotime($startDate)));

    $deptList = [];
    $rawData = [];

    $headers = [
        'Content-Type:application/json',
        'Accept:application/json'
    ];

    $url = "https://192.168.1.50:8098/api/transaction/list?endDate=20{$endDate}%2023%3A59%3A59&pageNo={$page}&pageSize=800&startDate=20{$startDate}%2000%3A00%3A00&access_token=97F30B9A7890AFBD347C53322B0C5653";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_VERBOSE => true,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    if (!isset($json['data'])) return [];

    foreach ($json["data"] as $val) {
        $deptList[] = $val["deptName"];
        $rawData[] = [
            "dept" => $val["deptName"],
            "sn"   => $val["devSn"],
            "name" => $val["devName"],
            "acc"  => $val["eventName"],
        ];
    }

    $uniqueDepts = array_unique($deptList);
    $total = count($uniqueDepts) - 1;
    $result = [];

    foreach ($uniqueDepts as $dept) {
        if ($dept === "") continue;

        $in = $out = $inn = $outt = $cur = $curr = 0;

        foreach ($rawData as $entry) {
            if (in_array($entry['acc'], ['Global Anti-Passback(logical)', 'Disconnected'])) continue;

            if ($entry['dept'] === $dept) {
                $devName = $entry["name"];

                if (preg_match("/TRIPOD-IN-POS-2/i", $devName) || preg_match("/MOBIL-IN-POS-2/i", $devName)) {
                    $in++;
                }

                if (preg_match("/TRIPOD-OUT-POS-2/i", $devName) || preg_match("/MOBIL-OUT-POS-2/i", $devName)) {
                    $out++;
                }
            }
        }

        $cur += ($in - $out);
        $curr += ($inn - $outt);

        $result["data"][] = [
            "dept"  => $dept,
            "in"    => $in,
            "out"   => $out,
            "cur"   => $cur,
            "inn"   => $inn,
            "outt"  => $outt,
            "curr"  => $curr,
            "tot"   => $total
        ];
    }

    return json_decode(json_encode($result, JSON_PRETTY_PRINT), true);
}

// Main aggregation
$in = $out = $cur = $tot = $page = 0;
$result = ["data" => []];

while (true) {
    $page++;
    $data = coun($page);

    if (empty($data)) break;

    foreach ($data["data"] as $entry) {
        $in += $entry["in"];
        $out += $entry["out"];
        $cur += $entry["cur"];
        if ($tot === 0) $tot = $entry["tot"];

        $result["data"][] = $entry;
    }
}

// Ambil hingga 100 nama departemen unik
$deptNames = array_unique(array_column(array_slice($result["data"], 0, 100), "dept"));

$resul = ["data" => []];

foreach ($deptNames as $dept) {
    $masuk = $keluar = $sisa = $masuk1 = $keluar1 = $sisa1 = $tota = 0;

    foreach ($result["data"] as $entry) {
        if ($entry["dept"] === $dept) {
            $masuk    += $entry["in"];
            $keluar   += $entry["out"];
            $sisa     += $entry["cur"];
            $masuk1   += $entry["inn"];
            $keluar1  += $entry["outt"];
            $sisa1    += $entry["curr"];
            if ($tota === 0) $tota = $entry["tot"];
        }
    }

    $resul["data"][] = [
        "dept"      => $dept,
        "in"        => $masuk,
        "out"       => $keluar,
        "cur"       => $sisa,
        "inn"       => $masuk1,
        "outt"      => $keluar1,
        "curr"      => $sisa1,
        "tot"       => $tota,
        "totalin"   => $in,
        "totalout"  => $out,
        "totalcur"  => $cur
    ];
}

$hasil = json_encode($resul, JSON_PRETTY_PRINT);
$data = json_decode($hasil, true);

// Untuk debug manual: print_r($data);
// Output $data jika ingin digunakan lebih lanjut
?>
