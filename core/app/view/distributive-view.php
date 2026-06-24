<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}
$products = ProductData::getAll();
$branches = BranchData::getAllActive();
?>
<div class="row">
	<div class="col-md-12">
		<h1><i class="bi bi-grid-3x3-gap"></i> Resumen por Sucursales (Distributivo)</h1>
		<br>
		<div class="card">
			<div class="card-header">DISTRIBUTIVO DE STOCK DE PRODUCTOS</div>
			<div class="card-body p-0">
				<?php if(count($products) > 0 && count($branches) > 0): ?>
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-sm mb-0">
							<thead>
								<th>Código</th>
								<th>Nombre del Producto</th>
								<?php foreach($branches as $branch): ?>
									<th><?php echo htmlspecialchars($branch->name); ?></th>
								<?php endforeach; ?>
								<th>Stock Total</th>
							</thead>
							<tbody>
								<?php foreach($products as $product): ?>
								<tr>
									<td><?php echo $product->id; ?></td>
									<td><?php echo htmlspecialchars($product->name); ?></td>
									<?php 
									$total_q = 0;
									foreach($branches as $branch): 
										$q = OperationData::getQYesF($product->id, $branch->id);
										$total_q += $q;
									?>
										<td><?php echo $q; ?></td>
									<?php endforeach; ?>
									<td><strong><?php echo $total_q; ?></strong></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="p-4 text-center">
						<p class="alert alert-warning m-0">No hay productos o sucursales activas registradas.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
