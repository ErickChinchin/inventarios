<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}

if(count($_POST)>0){
	$branch = new BranchData();
	$branch->name = $_POST["name"];
	$branch->address = $_POST["address"];
	$branch->phone = $_POST["phone"];
	$branch->is_active = 1;
	$branch->add();
	$_SESSION["success"] = "Sucursal agregada correctamente";
	print "<script>window.location='index.php?view=branches';</script>";
}
?>
