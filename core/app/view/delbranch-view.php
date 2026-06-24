<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}

$branch = BranchData::getById($_GET["id"]);
if($branch->id == 1){
    $_SESSION["error"] = "No se puede desactivar la sucursal principal.";
    print "<script>window.location='index.php?view=branches';</script>";
    exit;
}

$branch->is_active = $branch->is_active == 1 ? 0 : 1;
$branch->update();
$_SESSION["deleted"] = "Estado de la sucursal actualizado correctamente";
print "<script>window.location='index.php?view=branches';</script>";
?>
