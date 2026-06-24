<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}

$selected_branch_id = (isset($_GET["branch_id"]) && $_GET["branch_id"] !== "") ? intval($_GET["branch_id"]) : null;

$products = ProductData::getAll();
$total_products = count($products);
$total_items = 0;
$low_stock_count = 0;
$inventory_value = 0;

foreach($products as $product) {
	$q = OperationData::getQYesF($product->id, $selected_branch_id);
	$total_items += $q;
	if($q <= $product->inventary_min) {
		$low_stock_count++;
	}
	$inventory_value += ($q * $product->price_in);
}
?>

<div class="row">
	<div class="col-md-12">

		<h1><i class="bi bi-graph-up"></i> Inventario de Productos</h1>
		
		<div class="d-flex justify-content-between align-items-center mb-3 row">
			<div class="col-md-4">
				<form method="get" action="index.php" class="form-inline">
					<input type="hidden" name="view" value="inventary">
					<div class="form-group mb-0">
						<label for="branch_id" class="me-2 fw-semibold">Sucursal:</label>
						<select name="branch_id" class="form-control" onchange="this.form.submit()">
							<option value="">-- Todas las Sucursales (Global) --</option>
							<?php foreach(BranchData::getAllActive() as $branch): ?>
								<option value="<?php echo $branch->id; ?>" <?php if($selected_branch_id == $branch->id) echo "selected"; ?>><?php echo htmlspecialchars($branch->name); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</form>
			</div>
			<div class="col-md-8 text-end">
				<a href="report/inventary-pdf.php<?php echo $selected_branch_id ? '?branch_id='.$selected_branch_id : ''; ?>" target="_blank" class="btn btn-success text-white"><i class="bi bi-download"></i> Descargar PDF</a>
			</div>
		</div>
		<div class="clearfix"></div>

		<!-- Tarjetas de Métricas -->
		<div class="row mb-4">
			<div class="col-md-3">
				<div class="card shadow-sm border-0">
					<div class="card-body p-3 d-flex align-items-center">
						<div class="bg-primary text-white p-3 me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
							<i class="bi bi-tag fs-4"></i>
						</div>
						<div>
							<div class="fs-5 fw-bold text-primary"><?php echo $total_products; ?></div>
							<div class="text-medium-emphasis text-uppercase fw-semibold small" style="font-size: 0.75rem;">Productos en Catálogo</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card shadow-sm border-0">
					<div class="card-body p-3 d-flex align-items-center">
						<div class="bg-success text-white p-3 me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
							<i class="bi bi-boxes fs-4"></i>
						</div>
						<div>
							<div class="fs-5 fw-bold text-success"><?php echo $total_items; ?></div>
							<div class="text-medium-emphasis text-uppercase fw-semibold small" style="font-size: 0.75rem;">Unidades en Stock</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card shadow-sm border-0">
					<div class="card-body p-3 d-flex align-items-center">
						<div class="bg-danger text-white p-3 me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
							<i class="bi bi-exclamation-triangle fs-4"></i>
						</div>
						<div>
							<div class="fs-5 fw-bold text-danger"><?php echo $low_stock_count; ?></div>
							<div class="text-medium-emphasis text-uppercase fw-semibold small" style="font-size: 0.75rem;">Bajo Stock / Alertas</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card shadow-sm border-0">
					<div class="card-body p-3 d-flex align-items-center">
						<div class="bg-warning text-white p-3 me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
							<i class="bi bi-currency-dollar fs-4"></i>
						</div>
						<div>
							<div class="fs-5 fw-bold text-warning">$<?php echo number_format($inventory_value, 2); ?></div>
							<div class="text-medium-emphasis text-uppercase fw-semibold small" style="font-size: 0.75rem;">Valor del Inventario</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabla de Inventario -->
		<div class="card">
			<div class="card-header">INVENTARIO</div>
			<div class="card-body p-0">
				<?php if($total_products > 0): ?>
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-sm mb-0">
							<thead>
								<th>Código</th>
								<th>Nombre del Producto</th>
								<th>Precio Compra</th>
								<th>Precio Venta</th>
								<th>Mínimo</th>
								<th>Disponible</th>
								<th>Estado</th>
								<th>Acciones</th>
							</thead>
							<tbody>
								<?php foreach($products as $product):
									$q = OperationData::getQYesF($product->id, $selected_branch_id);
									
									// Determinar el badge de estado
									if($q == 0) {
										$status_badge = '<span class="badge bg-danger">Sin Stock</span>';
										$row_class = 'table-danger';
									} else if($q <= $product->inventary_min) {
										$status_badge = '<span class="badge bg-warning text-dark">Bajo Stock</span>';
										$row_class = 'table-warning';
									} else {
										$status_badge = '<span class="badge bg-success">Excelente</span>';
										$row_class = '';
									}
								?>
								<tr class="<?php echo $row_class; ?>">
									<td><?php echo $product->id; ?></td>
									<td><?php echo $product->name; ?></td>
									<td>$ <?php echo number_format($product->price_in, 2); ?></td>
									<td>$ <?php echo number_format($product->price_out, 2); ?></td>
									<td><?php echo $product->inventary_min; ?></td>
									<td><strong><?php echo $q; ?></strong></td>
									<td><?php echo $status_badge; ?></td>
									<td style="width: 180px;">
										<a href="index.php?view=history&product_id=<?php echo $product->id; ?><?php echo $selected_branch_id ? '&branch_id='.$selected_branch_id : ''; ?>" class="btn btn-xs btn-success text-white" title="Historial">
											<i class="bi bi-clock-history"></i> Historial
										</a>
										<a href="index.php?view=re&product=<?php echo urlencode($product->name); ?><?php echo $selected_branch_id ? '&branch_id='.$selected_branch_id : ''; ?>" class="btn btn-xs btn-primary text-white" title="Reabastecer">
											<i class="bi bi-plus-circle"></i> Comprar
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="p-4 text-center">
						<div class="jumbotron m-0">
							<h2>No hay productos</h2>
							<p>No se han agregado productos a la base de datos.</p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
