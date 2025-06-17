<?php
//session_start();
//if (isset($_SESSION["sesi"])){
//  header("Location: index.php"); die();
//}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Counting People</title>
    <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="plugins/slick/slick.css">
    <link rel="stylesheet" href="plugins/slick/slick-theme.css">
    <link rel="stylesheet" href="plugins/fancybox/jquery.fancybox.min.css">
    <link rel="stylesheet" href="plugins/aos/aos.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fav.png" />
    <link href="css/style.css" rel="stylesheet">
    <script src="assets/js/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script>
    $('document').ready(function () {
      setTimeout("getData()",1000);
      setInterval(function () { getData()}, 10000); // panggil setiap 10 detik
    }); 
    function getData() {
        $.ajax({
            url: "data/get_data.php",
            type: "GET",
            success: function (response) {
                $("#real").html(response)
            }
        });
    }
    </script>
</head>
<body>
<div class="body-wrapper" data-spy="scroll" data-target=".privacy-nav">
    <div class="p-2 shadow pl-5 pr-5">
        <div class="row no-gutters">
            <div class="col-6 col-sm-6 col-md-6 col-xs-6">
                <a class="navbar-brand" href="/"><img src="images/logo-pusharlis-1.png" alt="logo"></a>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-xs-6 text-right mt-2">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="ti-menu text-center"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav text-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><b>Area Pusharlis</b></a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="/merah"><b>Zona Merah</b></a>
                    </li> -->
                </ul>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row no-gutters mt-1">
          <div class="col-12 col-sm-12 col-md-12 col-xs-12">
            <h2 class="font-weight-bold text-center">
              COUNTING PEOPLE AREA PUSHARLIS
            </h2>
          </div>
        </div>
      </div>
      <div class="row no-gutters mt-1">
        <div class="col-12 col-sm-12 col-md-12 col-xs-12">
          <div class="card border-success bg-info">
            <div class="col-12 col-sm-12 col-md-12 col-xs-12 text-center pt-1">
              <h4 class="card-subtitle mb-2 text-white font-weight-bold">
              <span id="tanggalwaktu"></span> / <span id="jam"></span> : <span id="menit"></span> : <span id="detik"></span> <span>WIB</span>
              </h4>
            </div>
          </div>
        </div>
      </div>
      <div id="real">
      </div>
</div>
<script>
  window.setTimeout("waktu()", 1000);
  window.setTimeout("data()", 1000);
  function waktu() {
      var waktu = new Date();
      setTimeout("waktu()", 1000);
      document.getElementById("jam").innerHTML = waktu.getHours();
      document.getElementById("menit").innerHTML = waktu.getMinutes();
      document.getElementById("detik").innerHTML = waktu.getSeconds();
  }
  function data() {
      var tw = new Date();
      setTimeout("data()", 1000);
      if (tw.getTimezoneOffset() == 0) (a=tw.getTime() + ( 7 *60*60*1000))
      else (a=tw.getTime());
      tw.setTime(a);
      var tahun= tw.getFullYear ();
      var hari= tw.getDay ();
      var bulan= tw.getMonth ();
      var tanggal= tw.getDate ();
      var hariarray=new Array("Minggu,","Senin,","Selasa,","Rabu,","Kamis,","Jum'at,","Sabtu,");
      var bulanarray=new Array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");
      document.getElementById("tanggalwaktu").innerHTML = hariarray[hari]+" "+tanggal+" "+bulanarray[bulan]+" "+tahun;
  }
</script>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/bootstrap.min.js"></script>
<script src="plugins/slick/slick.min.js"></script>
<script src="plugins/fancybox/jquery.fancybox.min.js"></script>
<script src="plugins/syotimer/jquery.syotimer.min.js"></script>
<script src="plugins/aos/aos.js"></script>
<script src="js/script.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-3.6.1.min.js"></script>
</div>
</body>
<html>
