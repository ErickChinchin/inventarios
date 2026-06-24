<?php
if(isset($_GET["dni"]) && $_GET["dni"]!=""){
	$clients = PersonData::getClientsByDni($_GET["dni"]);
	if(count($clients)>0){
		?>
		<table class="table table-sm">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Cédula de Identidad</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($clients as $client):?>
				<tr>
					<td><?php echo $client->name." ".$client->lastname;?></td>
					<td><?php echo $client->dni;?></td>
					<td>
						<button type="button" class="btn btn-primary btn-sm select-client select-client-pos" 
							data-id="<?php echo $client->id;?>" 
							data-name="<?php echo $client->name." ".$client->lastname;?>"
							data-dni="<?php echo $client->dni;?>">
							Seleccionar
						</button>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<?php
	}else{
		echo "<p class='alert alert-warning'>No se encontró cliente con esa Cédula de Identidad</p>";
	}
}
?>