<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}

$branch = BranchData::getById($_GET["id"]);
?>
<div class="row">
	<div class="col-md-12">
	<h1>Editar Sucursal</h1>
	<br>
<div class="card">
  <div class="card-header">
    EDITAR SUCURSAL
  </div>
    <div class="card-body">
		<form class="form-horizontal" method="post" id="editbranch" action="index.php?view=updatebranch" role="form">
  <div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" value="<?php echo htmlspecialchars($branch->name); ?>" required class="form-control" id="name" placeholder="Nombre de la Sucursal">
    </div>
  </div>
  <div class="form-group row">
    <label for="address" class="col-sm-2 col-form-label">Dirección</label>
    <div class="col-md-6">
      <input type="text" name="address" value="<?php echo htmlspecialchars($branch->address); ?>" class="form-control" id="address" placeholder="Dirección">
    </div>
  </div>
  <div class="form-group row">
    <label for="phone" class="col-sm-2 col-form-label">Teléfono</label>
    <div class="col-md-6">
      <input type="text" name="phone" value="<?php echo htmlspecialchars($branch->phone); ?>" class="form-control" id="phone" placeholder="Teléfono">
    </div>
  </div>
  <div class="form-group row">
    <label for="is_active" class="col-sm-2 col-form-label">Activa</label>
    <div class="col-md-6">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php echo $branch->is_active == 1 ? "checked" : ""; ?>>
        <label class="form-check-label" for="is_active">
          Marcar como activa
        </label>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
      <input type="hidden" name="id" value="<?php echo $branch->id; ?>">
      <button type="submit" class="btn btn-primary">Actualizar Sucursal</button>
    </div>
  </div>
</form>
 </div>
</div>
	</div>
</div>
