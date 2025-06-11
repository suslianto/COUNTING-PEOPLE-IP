<?php
  $url = $_SERVER["HTTP_HOST"];
  if (empty($_SERVER["REQUEST_SCHEME"])){
    $ssl = "http://";
  }else{
    $ssl = $_SERVER["REQUEST_SCHEME"]."://";
  }
  $run = $ssl.$url."/";
?>
    <nav class="navbar navbar-expand-md navbar-light navw sh fixed-top">
      <a class="navbar-brand" href="<?= $run; ?>"><img class="logo__name" src="<?= $run; ?>assets/img/name.png" alt="RizkyDev" /></a>
      <button id="btn-nav" class="navbar-toggler sh" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <div class="text-center" id="op">
          <i class="fas fa-align-justify"></i>
        </div>
        <div class="text-center d-none" id="cs">
          <i class="fas fa-times"></i>
        </div>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link active" href="<?= $run; ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $run."tentang"; ?>">Tentang</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $run."project"; ?>">Project</a>
          </li>
        </ul>
      </div>
    </nav>