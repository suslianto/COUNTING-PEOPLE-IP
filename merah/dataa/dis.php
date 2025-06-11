<?php
header("Content-Type: application/json");
error_reporting(0);
function disab($page) {
        $full = array();
        $head = array('Content-Type:application/json','Accept:application/json');
        $chh = curl_init();
        curl_setopt_array($chh, array(
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_FOLLOWLOCATION => 0,
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_POST => 1,
          CURLOPT_TIMEOUT => 60,
          CURLOPT_URL => "https://192.168.1.50:8098/api/person/getPersonList?pageNo=".$page."&pageSize=800&access_token=416012CD899F5A14E96E09F08AF0D264",
          CURLOPT_HTTPHEADER => $head,
        ));
        $gett = curl_exec($chh);
        curl_close($chh);
        $data = json_decode($gett, true);
        foreach ($data["data"] as $val) {
              if ($val["isDisabled"] == true) {
                $full["data"][] = array(
                    "foto"=> $val["personPhoto"],
                    "name" => $val["name"],
                    "dept" => $val["deptName"],
                );
              }
        }
        $hasil = json_encode($full, JSON_PRETTY_PRINT);
        $data = json_decode($hasil, true);
        return $data;
}
$peg = 0;
$perdisable = array();
while (true) {
  $peg += 1;
  $data = disab($peg);
  if (count($data) < 1) {
    break;
  } else {
    foreach ($data["data"] as $val) {
      $perdisable["data"][] = array(
        "dept"=> $val["dept"],
        "foto" => $val["foto"],
        "name"=> $val["name"],
      );
    }
  }
}
echo json_encode($perdisable, JSON_PRETTY_PRINT);
?>