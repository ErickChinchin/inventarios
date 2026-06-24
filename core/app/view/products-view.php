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

		<h1>Productos</h1>
<div class="mb-3">
	<a href="index.php?view=newproduct" class="btn btn-primary">Agregar Producto</a>
  <a href="report/products-pdf.php" target="_blank" class="btn btn-success text-white"><i class="fa fa-download"></i> Descargar PDF</a>
</div>
<br>

<div class="card">
	<div class="card-header">
		PRODUCTOS
	</div>
		<div class="card-body p-0">

<?php
$products = ProductData::getAll();
if(count($products)>0){
	?>
<div class="table-responsive">
<table class="table table-bordered table-hover table-sm mb-0">
	<thead>
		<th>Codigo</th>
		<th>Imagen</th>
		<th>Nombre</th>
		<th>Precio Entrada</th>
		<th>Precio Salida</th>
		<th>Categoria</th>
		<th>Minima</th>
		<th>Activo</th>
		<th></th>
	</thead>
  <tbody>
	<?php foreach($products as $product):?>
	<tr>
		<td><?php echo $product->barcode; ?></td>
		<td>
			<?php if($product->image!=""):?>
				<img src="storage/products/<?php echo $product->image;?>" style="width:64px;">
			<?php endif;?>
		</td>
		<td><?php echo $product->name; ?></td>
		<td>$ <?php echo number_format($product->price_in,2,'.',','); ?></td>
		<td>$ <?php echo number_format($product->price_out,2,'.',','); ?></td>
		<td><?php if($product->category_id!=null){echo $product->getCategory()->name;}else{ echo "<center>----</center>"; }  ?></td>
		<td><?php echo $product->inventary_min; ?></td>
		<td><?php if($product->is_active): ?><i class="bi bi-check-lg"></i><?php endif;?></td>
		

		<td style="width:160px;">
		<?php if($product->barcode != ""): ?>
			<button class="btn btn-sm btn-info text-white" onclick="showBarcodeModal('<?php echo $product->barcode; ?>', '<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>')" title="Ver Código de Barras / QR"><i class="bi bi-qr-code"></i></button>
		<?php endif; ?>
		<a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
		<a href="index.php?view=delproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
		</td>
	</tr>
	<?php endforeach;?>
  </tbody>
</table>
</div>
<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay productos</h2>
		<p>No se han agregado productos a la base de datos, puedes agregar uno dando click en el boton <b>"Agregar Producto"</b>.</p>
	</div>
	<?php
}

?>

		</div>
</div>

<script>
function showBarcodeModal(code, name) {
	Swal.fire({
		title: name,
		html: `
			<div class="text-center p-3">
				<h6 class="fw-bold mb-2 text-secondary">Código de Barras</h6>
				<div class="d-flex justify-content-center bg-white p-2 border rounded mb-3">
					<svg id="barcode-display"></svg>
				</div>
				<div class="mb-3 text-start">
					<p class="mb-1"><strong>Producto:</strong> ${name}</p>
					<p class="mb-1 text-muted"><strong>Código:</strong> ${code}</p>
				</div>
				<h6 class="fw-bold mb-2 text-secondary">Código QR</h6>
				<div class="d-flex justify-content-center bg-white p-3 border rounded">
					<div id="qrcode-display"></div>
				</div>
			</div>
		`,
		confirmButtonText: 'Cerrar',
		confirmButtonColor: '#5856d6',
		didOpen: () => {
			try {
				JsBarcode("#barcode-display", code, {
					format: "CODE128",
					width: 2,
					height: 60,
					displayValue: true
				});
			} catch (e) {
				console.error("Error generating barcode", e);
				document.getElementById("barcode-display").outerHTML = "<p class='text-danger'>Formato de código incompatible</p>";
			}
			try {
				new QRCode(document.getElementById("qrcode-display"), {
					text: code,
					width: 128,
					height: 128
				});
			} catch (e) {
				console.error("Error generating QR code", e);
			}
		}
	});
}
</script>

<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>
