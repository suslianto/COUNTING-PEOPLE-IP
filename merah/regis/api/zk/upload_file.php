<?php

header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

include 'dbconfig.php'; // include database connection file
$data = json_decode(file_get_contents("php://input"), true); // collect input parameters and convert into readable 

$fileName  =  $_FILES['file']['name'];
$tempPath  =  $_FILES['file']['tmp_name'];
$fileSize  =  $_FILES['file']['size'];
		
if(empty($fileName))
{
	$errorMSG = json_encode(array("message" => "please select file", "status" => false));	
	echo $errorMSG;
}
else
{
	$upload_path = __DIR__.'/../../file_tools/'; 
	
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
			
	echo json_encode(array("message" => "File Uploaded Successfully", "status" => true));
        
}

?>