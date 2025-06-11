<?php
header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST");
header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

// include database connection file
// $data = json_decode(file_get_contents("php://input"), true);
    if (empty($_POST["name"])){
      $name = @$_POST["name"];
      $plat = @$_POST["plat"];
      $gender = @$_POST["gender"];
      $time = new Datetime("now");
      $pin = rand(2000, 10000);
      $pins = (string)$pin;
      $target_dir = __DIR__.'/file_tools/'; 
      $fileName = $_FILES["filename"]["name"];
      $tempPath = $_FILES["filename"]['tmp_name'];
      //$myfile = @$_POST["filename"];
      if ($_POST["dept"] == "others") {
      	$dep = @$_POST["newdept"];
          $nom = 0;
          $dept = 0;
          $nilai = array();
          while (true) {
             $nom += 1;
             if ($nom >= 30) {
             	break;
             }
             $head = array('Accept: application/json');
             $ch = curl_init();
		     curl_setopt_array($ch, array(
			         CURLOPT_SSL_VERIFYHOST => 0,
			         CURLOPT_SSL_VERIFYPEER => 0,
			         CURLOPT_FOLLOWLOCATION => 0,
			         CURLOPT_RETURNTRANSFER => 1,
			         CURLOPT_URL => "https://36.93.9.28:8098/api/department/get/".(string)$nom."?access_token=735AD8D13A875AA3303880F76DE75DA6",
			         CURLOPT_HTTPHEADER => $head,
              ));
		      $ge = curl_exec($ch);
		      $jsn = json_decode($ge, true);
		      if ($jsn["code"] == 0) {
                    array_push($nilai, intval($jsn["data"]["code"]));
              }
           }
          while (true) {
       	     $dept += 1;
                if (!in_array($dept, $nilai)) {
                	 $field = json_encode(array(
                          "code"=>$dept,
                          "name"=>$dep,
                          "parentCode"=>"",
                          "sortNo"=>999999
                      ));
                	 $url_add = "https://36.93.9.28:8098/api/department/add?access_token=735AD8D13A875AA3303880F76DE75DA6";
                     $header = array('Accept: application/json','Content-Type: application/json');
                     $ceh = curl_init();
                     curl_setopt($ceh, CURLOPT_SSL_VERIFYHOST, 0);
	                 curl_setopt($ceh, CURLOPT_SSL_VERIFYPEER, 0);
	                 curl_setopt($ceh, CURLOPT_FOLLOWLOCATION, 0);
                     curl_setopt($ceh, CURLOPT_URL, $url_add);
                     curl_setopt($ceh, CURLOPT_HTTPHEADER, $header);
                     curl_setopt($ceh, CURLOPT_RETURNTRANSFER, true);
                     curl_setopt($ceh, CURLOPT_POSTFIELDS, $field);
                     $respon = curl_exec($ceh);
                     $data_dep = json_decode($respon, true);
                     // var_dump($data_dep);
                     if ($data_dep['message'] == 'Department name already exists') {
                     	echo "
                           <script>
                              Toast.fire({
                                 icon: 'error',
                                 title: 'Department name already exists'
                               })
                              setTimeout(function() {window.location.href = '/';}, 2000);
                           </script>
                         ";
                     } else if ($data_dep['message'] == 'success') {}
                     break;
                }
          }
      } else {
      	$dept = @$_POST["dept"];
      }
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
      // store_uploaded_image('filename', 4000, 3000);
      // uploadAndResize('filename', $upload_path, 3000);
      if(!isset($errorMSG)) {
          $base64 = base64_encode($imagedata);
          // var_dump($base64);
	      /** echo json_encode(array("message" => "File Uploaded Successfully", "status" => true)); */
	      $url = "https://36.93.9.28:8098/api/person/add?access_token=735AD8D13A875AA3303880F76DE75DA6";
          $parameters = json_encode(array(
            "accEndTime"=> "2023-07-14 08:56:00",
            "accLevelIds"=>"1",
            "accStartTime"=> "2022-07-14 08:56:00",
            "birthday"=> "2016-07-15",
            "carPlate"=> $plat,
            "cardNo"=> "",
            "certNumber"=> "",
            "certType"=> 2,
            "deptCode"=> $dept,
            "email"=> "",
            "gender"=> $gender,
            "hireDate"=> "2019-06-10",
            "isDisabled"=> true,
            "isSendMail"=> false,
            "lastName"=> "",
            "mobilePhone"=> "",
            "name"=> $name,
            "personPhoto"=> $base64,
            "personPwd"=> "",
            "pin"=> $pins,
            "ssn"=> "111111",
            "supplyCards"=> ""
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
          $data_json = json_decode($response, true);
          // var_dump($data_json);
          $files = glob($target_dir.'*');
          foreach ($files as $file) {
               if (is_file($file))
               unlink($file);
          }
          $query = "TRUNCATE TABLE file";
          mysqli_query($conn, $query);
          $query = "ALTER TABLE file DROP id";
          mysqli_query($conn, $query);
          $query = "ALTER TABLE file ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
          mysqli_query($conn, $query);
          if ($data_json["message"] == "success") {
           	    // $_SESSION["sesi"] = true;
                     $field = json_encode(array(
                          "code"=>"1",
                          "pins"=>$pins,
                      ));
                	 $url_add = "https://36.93.9.28:8098/api/insAreaPerson/setAreaPerson?access_token=735AD8D13A875AA3303880F76DE75DA6";
                     $header = array('Accept: application/json','Content-Type: application/json');
                     $ceh = curl_init();
                     curl_setopt($ceh, CURLOPT_SSL_VERIFYHOST, 0);
	                 curl_setopt($ceh, CURLOPT_SSL_VERIFYPEER, 0);
	                 curl_setopt($ceh, CURLOPT_FOLLOWLOCATION, 0);
                     curl_setopt($ceh, CURLOPT_URL, $url_add);
                     curl_setopt($ceh, CURLOPT_HTTPHEADER, $header);
                     curl_setopt($ceh, CURLOPT_RETURNTRANSFER, true);
                     curl_setopt($ceh, CURLOPT_POSTFIELDS, $field);
                     $respon = curl_exec($ceh);
                     $data = json_decode($respon, true);
                     // var_dump($data);
                     if ($data['message'] == 'Person does not exist') {
                     	echo "
                           <script>
                              Toast.fire({
                                 icon: 'error',
                                 title: 'Person does not exist'
                               })
                              setTimeout(function() {window.location.href = '/';}, 2000);
                           </script>
                         ";
                     } else if ($data['message'] == 'success') {}
                     $head = array('Accept: application/json','Content-Type: application/json');
                     $ch = curl_init();
		             curl_setopt_array($ch, array(
			             CURLOPT_SSL_VERIFYHOST => 0,
			             CURLOPT_SSL_VERIFYPEER => 0,
			             CURLOPT_FOLLOWLOCATION => 0,
			             CURLOPT_RETURNTRANSFER => 1,
			             CURLOPT_URL => "https://36.93.9.28:8098/api/accLevel/list?pageNo=1&pageSize=10&access_token=735AD8D13A875AA3303880F76DE75DA6",
			             CURLOPT_HTTPHEADER => $head,
                      ));
		             $ge = curl_exec($ch);
		             $jsn = json_decode($ge, true);
		             //var_dump($jsn);
                     foreach ($jsn["data"] as $value) {
                       	if ($value["name"] == "General") {
                       	    // var_dump($value);
					           $id = $value["id"];
					           /** $fields = json_encode(array(
                                   "levelIds"=>$id,
                                   "pin"=>$pins,
                               )); */
                          	 $url_acc = "https://36.93.9.28:8098/api/accLevel/syncPerson?levelIds=".$id."&pin=".$pins."&access_token=735AD8D13A875AA3303880F76DE75DA6";
                               $heade = array('Accept: application/json','Content-Type: application/json');
                               $chh = curl_init();
                               curl_setopt($chh, CURLOPT_SSL_VERIFYHOST, 0);
	                           curl_setopt($chh, CURLOPT_SSL_VERIFYPEER, 0);
	                           curl_setopt($chh, CURLOPT_FOLLOWLOCATION, 0);
                               curl_setopt($chh, CURLOPT_URL, $url_acc);
                               curl_setopt($chh, CURLOPT_POST, 1);
                               curl_setopt($chh, CURLOPT_HTTPHEADER, $heade);
                               curl_setopt($chh, CURLOPT_RETURNTRANSFER, true);
                               $respon_acc = curl_exec($chh);
                               $data_acc = json_decode($respon_acc, true);
                               // var_dump($data_acc);
                               if ($data_acc['message'] == 'success') {}
                               break;
                           }
                     }
                    echo "
                      <script>
                         Toast.fire({
                            icon: 'success',
                            title: 'Registrasi successfully'
                          })
                         setTimeout(function() {window.location.href = '/';}, 2000);
                      </script>
                    ";
           } else if ($data_json["code"] == -63) {
       	    echo "
                      <script>
                         Toast.fire({
                            icon: 'error',
                            title: 'Face fetection due to:No face detected'
                         })
                      </script>
                    ";
           } else if ($data_json["message"] == "please select file") {
           	echo "
                      <script>
                         Toast.fire({
                            icon: 'error',
                            title: 'please select file'
                         })
                      </script>
                    ";
            } else {
            	echo "
                      <script>
                         Toast.fire({
                            icon: 'error',
                            title: 'please select file'
                         })
                      </script>
                    ";
             }
      } else {
      	echo "
                      <script>
                         Toast.fire({
                            icon: 'error',
                            title: 'please select file'
                         })
                      </script>
                    ";
     }
    }
?>