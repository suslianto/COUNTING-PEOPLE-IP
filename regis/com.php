<?php
header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

$target_dir = __DIR__.'/file_tools/'; 
$fileName = $_FILES["filename"]["name"];
$tempPath = $_FILES["filename"]['tmp_name'];
if(!empty($fileName)) {
      include 'dbconfig.php'; 
      $data = json_decode(file_get_contents("php://input"), true);
      list($width, $height, $type, $attr) = getimagesize($tempPath);
      $query = "ALTER TABLE file AUTO_INCREMENT=0";
      mysqli_query($conn, $query); 
	  $query = mysqli_query($conn,'INSERT into file (file) VALUES("'.$fileName.'")');
      $query = "SELECT * FROM file";
      $hasil = mysqli_query($conn, $query);
      $r = mysqli_fetch_array($hasil);
      if ($width >= 3000) {
           include('compress.php');
           CreateThumbnail($tempPath,$target_dir.$fileName,3000);
           $imagedata = file_get_contents($target_dir.$r[file]);
      } else {
      	 move_uploaded_file($tempPath, $target_dir . $fileName);
           $imagedata = file_get_contents($target_dir.$fileName);
      }
      $base = base64_encode($imagedata);
      echo json_encode(array("data" => $base, "status" => true));
}
?>