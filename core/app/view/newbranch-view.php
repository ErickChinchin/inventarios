<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}
?>
<div class="row">
	<div class="col-md-12">
	<h1>Nueva Sucursal</h1>
	<br>
<div class="card">
  <div class="card-header">
    NUEVA SUCURSAL
  </div>
    <div class="card-body">
		<form class="form-horizontal" method="post" id="addbranch" action="index.php?view=addbranch" role="form">
  <div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre de la Sucursal">
    </div>
  </div>
  <div class="form-group row">
    <label for="address" class="col-sm-2 col-form-label">Dirección</label>
    <div class="col-md-6">
      <input type="text" name="address" class="form-control" id="address" placeholder="Dirección">
    </div>
  </div>
  <div class="form-group row">
    <label for="phone" class="col-sm-2 col-form-label">Teléfono</label>
    <div class="col-md-6">
      <input type="text" name="phone" class="form-control" id="phone" placeholder="Teléfono">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
      <button type="submit" class="btn btn-primary">Agregar Sucursal</button>
    </div>
  </div>
</form>   
 </div>
</div>
	</div>
</div>
