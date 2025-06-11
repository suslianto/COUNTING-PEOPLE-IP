<?php
error_reporting(0);
include "api.php";
// var_dump($data);
foreach ($resul["data"] as $valu) {
?>
      <div class="container-fluid">
            <div class="row no-gutters p-1">
              <div class="col-12 col-sm-12 col-md-4 col-xs-12 p-1">
                <div class="card border-success bg-success">
                  <div class="row">
                      <div class="col-6 col-sm-6 col-md-6 col-xs-12">
                        <img src="images/person_white.png" width="100px" class="img-fluid mx-auto d-block" alt="Responsive image">
                      </div>
                      <div class="col-6 col-sm-6 col-md-6 col-xs-12 text-center mt-4">
                        <h2 class="card-subtitle mb-2 text-white font-weight-bold"><?php echo $valu["totalin"]; ?></h2>
                        <h5 class="card-subtitle mb-2 text-white font-weight-bold">IN</h5>
                      </div>
                    </div>
                </div>
              </div>
              <div class="col-5S col-sm-12 col-md-4 col-xs-12 p-1">
                <div class="card border-success bg-warning">
                  <div class="row">
                      <div class="col-6 col-sm-6 col-md-6 col-xs-12">
                        <img src="images/person_white.png" width="100px" class="img-fluid mx-auto d-block" alt="Responsive image">
                      </div>
                      <div class="col-6 col-sm-6 col-md-6 col-xs-12 text-center mt-4">
                        <h2 class="card-subtitle mb-2 text-white font-weight-bold"><?php echo $valu["totalcur"]; ?></h2>
                        <h5 class="card-subtitle mb-2 text-white font-weight-bold">CURRENTS</h5>
                      </div>
                    </div>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-4 col-xs-12 p-1">
                <div class="card border-success bg-primary">
                  <div class="row">
                      <div class="col-6 col-sm-6 col-md-6 col-xs-10">
                        <img src="images/person_white.png" width="100px" class="img-fluid mx-auto d-block" alt="Responsive image">
                      </div>
                      <div class="col-6 col-sm-6 col-md-6 col-xs-12 text-center mt-4">
                        <h2 class="card-subtitle mb-2 text-white font-weight-bold"><?php echo $valu["totalout"]; ?></h2>
                        <h5 class="card-subtitle mb-2 text-white font-weight-bold">OUT</h5>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row no-gutters">
            <div class="col-12 col-sm-12 col-md-12 col-xs-12 p-1">
              <div class="card border-success bg-primary">
                <div class="col-12 col-sm-12 col-md-12 col-xs-12 pt-1"></div>
              </div>
            </div>
          </div>
<?php
  break;
}
?>
<div class="horizontal-center"></div>
</div>
<div class="card-body">
  <table class="table table-borderless table-striped text-center bg-cover">
    <thead>
      <tr>
        <th scope="col"><strong>DEPARTMENT</strong></th>
        <td scope="col"><strong>IN</strong></td>
        <td scope="col"><strong>OUT</strong></td>
        <td scope="col"><strong>CURRENTS</strong></td>
      </tr>
    </thead>
    <tbody>
        <?php
        foreach ($resul["data"] as $value) {
          ?>
                    <tr>
                      <td class="text-left bolded"><strong><?php echo $value["dept"]; ?></strong></td>
                      <td class="bolded"><strong><?php echo $value["in"]; ?></strong></td>
                      <td class="bolded"><strong><?php echo $value["out"]; ?></strong></td>
                      <td class="bolded"><strong><?php echo $value["cur"]; ?></strong></td>
                    </tr>
                  <?php
                    // array_push($nilai, intval($json["data"]["code"]));
        }
        ?>
    </tbody>
  </table>
  </div>