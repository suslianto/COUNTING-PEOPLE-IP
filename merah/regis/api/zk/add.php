<?php
header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");
include 'dbconfig.php';
$name = $_POST["name"];
$pin = $_POST["pin"];
$plat = $_POST["plat"];
$code = $_POST["code"];
$photo = $_POST["photo"];
$url = "https://36.93.9.28:8098/api/person/add?access_token=735AD8D13A875AA3303880F76DE75DA6";
$parameters = array(
"accEndTime"=> "2023-07-14 08:56:00",
  "accLevelIds"=>"1",
  "accStartTime"=> "2022-07-14 08:56:00",
  "birthday"=> "2016-07-15",
  "carPlate"=> plat,
  "cardNo"=> "",
  "certNumber"=> 123456,
  "certType"=> 2,
  "deptCode"=> code,
  "email"=> "",
  "gender"=> gender,
  "hireDate"=> "2019-06-10",
  "isDisabled"=> false,
  "isSendMail"=> true,
  "lastName"=> "",
  "mobilePhone"=> "",
  "name"=> name,
  "personPhoto"=> photo,
  "personPwd"=> "123456",
  "pin"=> pin,
  "ssn"=> "111111",
  "supplyCards"=> "987643"
);
$options = array('http' => array(
    'header'  => 'Content-Type: application/json\r\n',
    'method'  => 'POST',
    'content' => http_build_query($parameters)
));

$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context), TRUE);
?>