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
		<h1>Lista de Usuarios</h1>
	<a href="index.php?view=newuser" class="btn btn-secondary "><i class='bi bi-user'></i> Nuevo Usuario</a>
	<br><br>
<div class="card">
	<div class="card-header">USUARIOS
	</div>
		<div class="card-body p-0">


		<?php

		$users = UserData::getAll();
		if(count($users)>0){
			// si hay usuarios
			?>
			<div class="table-responsive">
			<table class="table table-bordered table-hover table-sm mb-0">
<thead>
  			<th>Nombre completo</th>
  			<th>Nombre de usuario</th>
  			<th>Email</th>
  			<th>Activo</th>
  			<th>Perfil</th>
  			<th>Sucursal</th>
  			<th></th>
  			</thead>
			<tbody>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo $user->name." ".$user->lastname; ?></td>
				<td><?php echo $user->username; ?></td>
				<td><?php echo $user->email; ?></td>
				<td>
					<?php if($user->is_active):?>
						<i class="bi bi-check-lg"></i>
					<?php endif; ?>
				</td>
				<td>
				<?php 
				if($user->kind == 0) echo "Administrador";
				elseif($user->kind == 1) echo "Vendedor";
				elseif($user->kind == 2) echo "Cliente";
				else echo "Usuario";
				?>
			</td>
			<td>
				<?php
				$branch = $user->getBranch();
				if($branch != null){
					echo htmlspecialchars($branch->name);
				} else {
					echo "--";
				}
				?>
			</td>
				<td style="width:130px;">
					<a href="index.php?view=edituser&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
					<?php if($user->id != $_SESSION["user_id"]):?>
					<a href="index.php?view=deluser&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
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
			// no hay usuarios
		}


		?>
		</div>
</div>


	</div>
</div>
