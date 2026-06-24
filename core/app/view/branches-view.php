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
		<h1>Sucursales</h1>
<div class="">
	<a href="index.php?view=newbranch" class="btn btn-secondary"><i class='fa fa-building'></i> Nueva Sucursal</a>
</div>
<br>
<div class="card">
	<div class="card-header">
		SUCURSALES
	</div>
		<div class="card-body p-0">
		<?php
		$branches = BranchData::getAll();
		if(count($branches)>0){
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-sm mb-0">
			<thead>
			<th>Nombre</th>
			<th>Dirección</th>
			<th>Teléfono</th>
			<th>Estado</th>
			<th>Acciones</th>
			</thead>
			<tbody>
			<?php
			foreach($branches as $branch){
				?>
				<tr>
				<td><?php echo $branch->name; ?></td>
				<td><?php echo $branch->address; ?></td>
				<td><?php echo $branch->phone; ?></td>
				<td>
					<?php if($branch->is_active == 1): ?>
						<span class="badge badge-success">Activa</span>
					<?php else: ?>
						<span class="badge badge-danger">Inactiva</span>
					<?php endif; ?>
				</td>
				<td style="width:180px;">
					<a href="index.php?view=editbranch&id=<?php echo $branch->id;?>" class="btn btn-warning btn-xs">Editar</a>
					<?php if($branch->id != 1): ?>
						<a href="index.php?view=delbranch&id=<?php echo $branch->id;?>" class="btn btn-danger btn-xs">
							<?php echo $branch->is_active == 1 ? "Desactivar" : "Activar"; ?>
						</a>
					<?php endif; ?>
				</td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			</div>
		<?php
		}else{
			echo "<p class='alert alert-danger'>No hay Sucursales</p>";
		}
		?>
		</div>
</div>
	</div>
</div>
