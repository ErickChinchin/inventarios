<?php
$total = 0;
$cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : array();
?>
<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-8">RESUMEN DE VENTA</div>
      <div class="col-4 text-end">
        <a href="index.php?view=clearcartpos" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if(count($cart)>0): ?>
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr class="bg-light">
            <th>Cant.</th>
            <th>Producto</th>
            <th class="text-end">Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cart as $p):
            $product = ProductData::getById($p["product_id"]);
          ?>
          <tr>
            <td><?php echo $p["q"]; ?></td>
            <td class="small"><?php echo $product->name; ?></td>
            <td class="text-end fw-bold">$ <?php 
              $pt = $product->price_out * $p["q"]; 
              $total += $pt; 
              echo number_format($pt, 2); 
            ?></td>
            <td class="text-end">
              <button class="btn btn-link btn-sm text-danger p-0" onclick="deleteFromCart(<?php echo $product->id; ?>)">
                <i class="bi bi-x-circle"></i>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <div class="p-3 bg-light border-top">
        <div class="d-flex justify-content-between mb-1">
          <span>Subtotal:</span>
          <span>$ <?php echo number_format($total * 0.84, 2); ?></span>
        </div>
        <div class="d-flex justify-content-between mb-1">
          <span>IVA (16%):</span>
          <span>$ <?php echo number_format($total * 0.16, 2); ?></span>
        </div>
        <div class="d-flex justify-content-between h5 mt-2 pt-2 border-top">
          <span class="fw-bold">TOTAL:</span>
          <span class="fw-bold text-primary">$ <?php echo number_format($total, 2); ?></span>
        </div>
      </div>

      <div class="p-3">
        <form method="post" id="processsellpos" action="index.php?view=processsellpos">
          <div class="mb-3">
            <label class="form-label small">Cliente</label>
            <?php $clients = PersonData::getClients(); ?>
            <select name="client_id" class="form-select form-select-sm" id="client_select_pos">
              <option value="">-- NINGUNO --</option>
              <?php foreach($clients as $client):?>
                <option value="<?php echo $client->id;?>"><?php echo $client->name." ".$client->lastname; if($client->dni){ echo " (C.I.: ".$client->dni.")"; }?></option>
              <?php endforeach;?>
            </select>
            <button type="button" class="btn btn-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#searchClientModalPos">
                <i class="bi bi-search"></i> Buscar por Cédula
            </button>
          </div>
          <!-- Modal de búsqueda de clientes -->
          <div class="modal fade" id="searchClientModalPos" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Buscar Cliente por Cédula</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                      <label class="form-label">Cédula de Identidad</label>
                      <input type="text" id="dni_search_pos" class="form-control" placeholder="Ingrese cédula de identidad...">
                  </div>
                  <div id="client_search_results_pos"></div>
                </div>
              </div>
            </div>
          </div>
          <script>
          $(document).ready(function(){
              $("#dni_search_pos").on("keyup", function(e){
                  if(e.keyCode === 13){
                      searchClientByDniPos();
                  }
              });
              $(document).on("click", ".select-client-pos", function(){
                  var clientId = $(this).data("id");
                  $("#client_select_pos").val(clientId);
                  $("#searchClientModalPos").modal("hide");
              });
          });
          function searchClientByDniPos(){
              var dni = $("#dni_search_pos").val();
              if(dni.length < 1){ return; }
              $.get("./?action=searchclient", {dni: dni}, function(data){
                  $("#client_search_results_pos").html(data);
              });
          }
          </script>
          <div class="mb-3">
            <label class="form-label small">Descuento</label>
            <input type="number" name="discount" class="form-control form-control-sm" value="0" id="discount_pos">
          </div>
          <div class="mb-3">
            <label class="form-label small text-success fw-bold">Efectivo Recibido</label>
            <input type="number" name="money" required class="form-control" id="money_pos" step="0.01">
          </div>
          <input type="hidden" name="total" value="<?php echo $total; ?>">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-1"></i> FINALIZAR VENTA
          </button>
        </form>
      </div>
    <?php else: ?>
      <div class="p-5 text-center text-muted">
        <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
        <p class="mt-2">El carrito está vacío.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
	$("#processsellpos").submit(function(e){
		var discount = $("#discount_pos").val();
		var money = $("#money_pos").val();
    var total = <?php echo $total; ?>;
		if(parseFloat(money) < (total - parseFloat(discount))){
			Swal.fire('Error', 'El efectivo recibido es insuficiente.', 'error');
			e.preventDefault();
		}else{
			if(discount==""){ discount=0;}
      var cambio = parseFloat(money) - (total - parseFloat(discount));
			// Utilizar SweetAlert2 para la confirmación del cambio
      e.preventDefault();
      Swal.fire({
        title: 'Confirmar Venta',
        text: 'Cambio para el cliente: $' + cambio.toFixed(2),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
		}
	});
</script>
