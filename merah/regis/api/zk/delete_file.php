<?php

header("Content-Type: application/json");
include 'dbconfig.php';

$query = "SELECT * From file ORDER BY id DESC";
$hasil = mysqli_query($conn, $query);

$no = (int)$_GET['no'];
while ($r = mysqli_fetch_array($hasil)){
    if ($r[id] == $no) {
       $folder = __DIR__.'/../../file_tools/'; 
       $filename = $r[file];
       $file_extension = strtolower(substr(strrchr($filename,"."),1));
       if (!file_exists($folder.$filename)) {
          $errorMSG = json_encode(array("message" => "File not found", "status" => false));	
	  echo $errorMSG;
       } else if ($file_extension=='php'){
          $errorMSG = json_encode(array("message" => "File not foundt", "status" => false));	
	  echo $errorMSG;
       } else {
          unlink($folder.$filename);
          $query = "DELETE FROM file WHERE id=".$no;
          mysqli_query($conn, $query);
          $query = "ALTER TABLE file DROP id";
          mysqli_query($conn, $query);
          $query = "ALTER TABLE file ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
          mysqli_query($conn, $query);
          echo json_encode(array("message" => "Deleted file successful", "status" => true));
       }
    }
} 
?>