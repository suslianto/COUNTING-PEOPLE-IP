<?php
error_reporting(0);
function coun($page) {
        date_default_timezone_set("Asia/jakarta");
        $startDate = date('y-m-d');
        $endDate = date('y-m-d', strtotime("+1 day", strtotime($startDate)));
        $tes = array();
        $full = array();
        $head = array('Content-Type:application/json','Accept:application/json');
        $ch = curl_init();
        curl_setopt_array($ch, array(
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_FOLLOWLOCATION => 0,
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_TIMEOUT => 60,
          CURLOPT_URL => "https://192.168.1.50:8098/api/transaction/list?endDate=20".$endDate."%2023%3A59%3A59&pageNo=".$page."&pageSize=800&startDate=20".$startDate."%2000%3A00%3A00&access_token=416012CD899F5A14E96E09F08AF0D264",
          CURLOPT_HTTPHEADER => $head,
          CURLOPT_VERBOSE => true,
        ));
        $get = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($get, true);
        //var_dump($data);
	      $num = 0;
        foreach ($data["data"] as $val) {
            	$dept = $val["deptName"];
            	$sn = $val["devSn"];
            	$name = $val["devName"];
            	$acc = $val['eventName'];
            	$tes[] = $dept;
            	$full[] = array(
               		"dept"=>$dept,
               		"sn"=>$sn,
               		"name"=>$name,
               		"acc" => $acc,
            	);
        }
        $tes = array_unique($tes);
        $total = count($tes) - 1;
        $result = array();
        foreach ($tes as $val) {
            if ($val != "") {
               $in = 0;
               $out = 0;
               $cur = 0;
               $inn = 0;
               $outt = 0;
               $curr = 0;
               $sn = array();
               foreach ($full as $res) {
                if ($res['acc'] == 'Global Anti-Passback(logical)'){}
                else if ($res['acc'] == 'Disconnected'){}
                else {
                  if ($res["dept"] == $val) {
                    $pin1 = "MOBIL-IN-POS-1";
                    $pout1 = "MOBIL-OUT-POS-1";
                    $pin2 = "MOTOR-IN-POS-1";
                    $pout2 = "MOTOR-OUT-POS-1";
                    $pin3 = "TRIPOD-IN-POS-1";
                    $pout3 = "TRIPOD-OUT-POS-1";
                     if(preg_match("/$pin1/i", $res["name"])) {
                      $in += 1;
                    } else if (preg_match("/$pout1/i", $res["name"])) {
                      $out += 1;
                    }
                    if(preg_match("/$pin2/i", $res["name"])) {
                      $in += 1;
                    } else if (preg_match("/$pout2/i", $res["name"])) {
                      $out += 1;
                    }
                    if(preg_match("/$pin3/i", $res["name"])) {
                      $in += 1;
                    } else if (preg_match("/$pout3/i", $res["name"])) {
                      $out += 1;
                    }
                  }
                }
              }
              
              $cur += ($in - $out);
              $curr += ($inn - $outt);
               $result["data"][] = array(
                  "dept"=> $val,
                  "in"=> $in,
                  "out"=> $out,
                  "cur"=> $cur,
                  "inn"=> $inn,
                  "outt"=> $outt,
                  "curr"=> $curr,
                  "tot" => $total
               );
            }
        }
        $hasil = json_encode($result, JSON_PRETTY_PRINT);
        $data = json_decode($hasil, true);
        return $data;
}
$in = 0;
$cur = 0;
$out = 0;
$tot = 0;
$pag = 0;
$result = array();
$resul = array();
while (true) {
  $pag += 1;
  $data = coun($pag);
  if (count($data) < 1) {
    break;
  } else {
    foreach ($data["data"] as $val) {
      $in += $val["in"];
      $out += $val["out"];
      $cur += $val["cur"];
      if ($tot == 0) {
        $tot += $val["tot"];
      }
      $result["data"][] = array(
        "dept"=> $val["dept"],
        "in"=> $val["in"],
        "out"=> $val["out"],
        "cur"=> $val["cur"],
        "inn"=> $val["inn"],
        "outt"=> $val["outt"],
        "curr"=> $val["curr"],
        "tot" => $val["tot"]
      );
    }
  }
}
$deb = array();
$noo = 0;
foreach ($result["data"] as $val) {
  $noo += 1;
  $deb[] = $val["dept"];
  if ($noo >= 100) {
	   break;
  }
}
$db = array_unique($deb);
$nom = 0;
while (true) {
  foreach ($db as $key) {
      $masuk = 0;
      $keluar = 0;
      $sisa = 0;
      $masuk1 = 0;
      $keluar1 = 0;
      $sisa1 = 0;
      $tota = 0;
      $pag = 0;
      foreach ($result["data"] as $val) {
        if ($key == $val["dept"]) {
          $masuk += $val["in"];
          $keluar += $val["out"];
          $sisa += $val["cur"];
          $masuk1 += $val["inn"];
          $keluar1 += $val["outt"];
          $sisa1 += $val["curr"];
          if ($tota == 0) {
            $tota += $val["tot"];
          }
        }
      }
      $resul["data"][] = array(
        "dept"=> $key,
        "in"=> $masuk,
        "out"=> $keluar,
        "cur"=> $sisa,
        "inn"=> $masuk1,
        "outt"=> $keluar1,
        "curr"=> $sisa1,
        "tot" => $tota,
        "totalin" => $in,
        "totalout" => $out,
        "totalcur" => $cur,
      );
      // array_push($nilai, intval($json["data"]["code"]));
  }
   break;
}
$hasil = json_encode($resul, JSON_PRETTY_PRINT);
$data = json_decode($hasil, true);
?>