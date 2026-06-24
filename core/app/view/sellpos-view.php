<div class="row">
	<div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        <h1>Punto de Venta</h1>
        <p><b>Buscar producto por nombre o por código:</b></p>
        <form id="searchp" onsubmit="return false;">
          <input type="hidden" name="view" value="sellpos">
<?php
$curr_user = UserData::getById($_SESSION["user_id"]);
if($curr_user->kind != 0 && $curr_user->kind != 1){
    $_SESSION["error"] = "No tiene permisos para acceder a esta sección.";
    print "<script>window.location='index.php?view=home';</script>";
    exit;
}
?>
<div class="row">
            <div class="col-md-10">
              <input type="text" id="product_code" name="product" class="form-control" autocomplete="off" placeholder="Escriba el nombre o código del producto...">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
	</div>
</div>

<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-header">PRODUCTOS</div>
      <div class="card-body">
        <div id="show_search_results" class="row">
          <!-- Aquí se cargarán los productos en cuadrícula -->
          <div class="col-md-12 text-center text-muted">
            <p><i class="bi bi-cart4" style="font-size: 3rem;"></i></p>
            <p>Realice una búsqueda para comenzar.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div id="cart_summary">
      <!-- Aquí se cargará el resumen del carrito via AJAX -->
      <div class="card">
        <div class="card-header">RESUMEN</div>
        <div class="card-body text-center text-muted">
          <p>Cargando carrito...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Cargar carrito al iniciar
  updateCart();

  // Beep sound function using Web Audio API
  function playScanBeep() {
    try {
      var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      var oscillator = audioCtx.createOscillator();
      var gainNode = audioCtx.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(audioCtx.destination);

      oscillator.type = 'sine';
      oscillator.frequency.setValueAtTime(1000, audioCtx.currentTime); // Hz
      gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime); // volume
      
      oscillator.start();
      setTimeout(function(){
        oscillator.stop();
        audioCtx.close();
      }, 80);
    } catch(e) {
      console.error("Web Audio API not supported", e);
    }
  }

  function tryQuickAddByBarcode(code) {
    if(code.trim() === ""){ return; }
    $.post("./?action=quickaddpos", {barcode: code}, function(response){
      if(response.status === "success") {
        playScanBeep();
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
        Toast.fire({
          icon: 'success',
          title: response.name + ' agregado al carrito'
        });
        $("#product_code").val("");
        $("#product_code").focus();
        updateCart();
      } else if (response.status === "error_insufficient_stock") {
        Swal.fire({
          title: 'Stock Insuficiente',
          text: 'No hay stock disponible para ' + response.name,
          icon: 'warning',
          confirmButtonColor: '#5856d6'
        });
        $("#product_code").val("");
        $("#product_code").focus();
      } else {
        // Fallback to normal search
        searchProducts();
      }
    }, "json").fail(function() {
      searchProducts();
    });
  }

	$("#searchp").on("submit",function(e){
		e.preventDefault();
		var q = $("#product_code").val();
		tryQuickAddByBarcode(q);
    return false;
	});

  var typingTimer;
  var doneTypingInterval = 500;

  $("#product_code").on("keyup", function (e) {
    if(e.keyCode === 13) return;
    clearTimeout(typingTimer);
    if ($(this).val().length > 2) {
      typingTimer = setTimeout(searchProducts, doneTypingInterval);
    }
  });

  function searchProducts(){
    var q = $("#product_code").val();
    if(q==""){ return; }
    $.get("./?action=searchproductpos", {product: q}, function(data){
			$("#show_search_results").html(data);
		});
  }

  function addToCart(product_id){
    var q = $("#q-"+product_id).val();
    $.post("./?action=addtocartpos", {product_id: product_id, q: q}, function(data){
      if(data.trim() == "error_insufficient_stock"){
        Swal.fire('Atención', 'No hay suficiente stock disponible para agregar esa cantidad.', 'warning');
      } else {
        updateCart();
      }
    });
  }
  window.addToCart = addToCart;

  function updateCart(){
    $.get("./?action=cartpos", function(data){
      $("#cart_summary").html(data);
    });
  }
  window.updateCart = updateCart;

  function deleteFromCart(product_id){
    $.get("./?action=delfromcartpos", {product_id: product_id}, function(data){
      updateCart();
    });
  }
  window.deleteFromCart = deleteFromCart;
});
</script>
