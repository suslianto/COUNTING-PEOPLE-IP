<?php
header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");
$name = @$_POST["name"];
$dept = @$_POST["dept"];
$mytext = @$_POST["mytext"];
$gender = @$_POST["gender"];
//$myfile = @$_POST["filename"];
include 'dbconfig.php'; // include database connection file
$data = json_decode(file_get_contents("php://input"), true); // collect input parameters and convert into readable 

$fileName  =  $_FILES['filename']['name'];
$tempPath  =  $_FILES['filename']['tmp_name'];
$fileSize  =  $_FILES['filename']['size'];
		
if(empty($fileName))
{
	$errorMSG = json_encode(array("message" => "please select file", "status" => false));	
	echo $errorMSG;
}
else
{
	$upload_path = __DIR__.'/file_tools/'; 
	
	$fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension
		
	// valid image extensions
	$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'mp4', 'mp3', 'py', 'php', 'js', 'sh', 'mkv'); 
					
	// allow valid image file formats
	if(in_array($fileExt, $valid_extensions))
	{				
		//check file not exist our upload folder path
		if(!file_exists($upload_path . $fileName))
		{
			// check file size '5MB'
			if($fileSize < 100000000){
			     move_uploaded_file($tempPath, $upload_path . $fileName);
			}
			else{		
				$errorMSG = json_encode(array("message" => "Sorry, your file is too large, please upload 5 MB size", "status" => false));	
				echo $errorMSG;
			}
		}
		else
		{		
			$errorMSG = json_encode(array("message" => "Sorry, file already exists check upload folder", "status" => false));	
			echo $errorMSG;
		}
	}
	else
	{		
		$errorMSG = json_encode(array("message" => "Sorry, only JPG, JPEG, PNG, GIF, MP4, MP3, MKV, PY, PHP, JS, & SH files are allowed", "status" => false));	
		echo $errorMSG;		
	}
}
		
// if no error caused, continue ....
if(!isset($errorMSG))
{
    $query = "ALTER TABLE file AUTO_INCREMENT=0";
    mysqli_query($conn, $query); 
	$query = mysqli_query($conn,'INSERT into file (file) VALUES("'.$fileName.'")');
    $query = "SELECT * FROM file";
    $hasil = mysqli_query($conn, $query);
    $r = mysqli_fetch_array($hasil);
	$imagedata = file_get_contents($upload_path.$r[file]);
     // alternatively specify an URL, if PHP settings allow
    $base64 = base64_encode($imagedata);
    // var_dump($r);
	// echo json_encode(array("message" => "File Uploaded Successfully", "status" => true));
	$url = "https://36.93.9.28:8098/api/person/add?access_token=735AD8D13A875AA3303880F76DE75DA6";
    $parameters = json_encode(array(
      "accEndTime"=> "2023-07-14 08:56:00",
      "accLevelIds"=>"1",
      "accStartTime"=> "2022-07-14 08:56:00",
      "birthday"=> "2016-07-15",
      "carPlate"=> "A4356BH",
      "cardNo"=> "123456789",
      "certNumber"=> 123456,
      "certType"=> 2,
      "deptCode"=> $dept,
      "email"=> "",
      "gender"=> $gender,
      "hireDate"=> "2019-06-10",
      "isDisabled"=> false,
      "isSendMail"=> false,
      "lastName"=> "",
      "mobilePhone"=> "",
      "name"=> $name,
      "personPhoto"=> $base64,
      "personPwd"=> "",
      "pin"=> "1234574",
      "ssn"=> "111111",
      "supplyCards"=> "987643"
    ));
    $head = array('Accept: application/json','Content-Type: application/json');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    $response = curl_exec($ch);
    var_dump($response);
    $query = "SELECT * From file ORDER BY id DESC";
    $hasil = mysqli_query($conn, $query);

    while ($r = mysqli_fetch_array($hasil)){
        if ($r[id] == 0) {
           $folder = __DIR__.'/file_tools/'; 
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
}
  ?>
