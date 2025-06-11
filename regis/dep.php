<?php
header("Content-Type: application/json");
$host = "127.0.0.1";
$base = "biosecurity-boot";
$user = "root";
$pwd = "ZKTeco##123";
$head = array('Content-Type: application/json;charset=UTF-8');

$conn = pg_connect("host=".$host." port=5442 dbname=".$base." user=".$user." password=".$pwd) or die("Failed:".pg_last_error()."<br/>");
//print "Sucess.<br/>";

$q = "SELECT * FROM pers_person WHERE pin = '6577'";
$has = pg_query($q) or die("Error:". pg_last_error());
$res = pg_fetch_row($has);
print_r($res);
$qur = "UPDATE pers_attribute_ext SET attr_value15 = '567fg6' WHERE person_id = '$res[0]'";
$ha = pg_query($qur) or die("Error:". pg_last_error());
$re = pg_fetch_row($ha);
print_r($re);
?>