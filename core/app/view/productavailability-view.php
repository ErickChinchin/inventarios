<?php
if (!isset($curr_user)) {
    $curr_user = isset($_SESSION['user_id']) ? UserData::getById($_SESSION['user_id']) : null;
}
?>

<div class="row">
  <div class="col-md-12">
    <h1>Disponibilidad de Productos</h1>
    <p class="text-muted">Verifique si hay disponibilidad de productos sin ver cantidades ni precios.</p>
    
    <div class="card">
      <div class="card-header">
        <form class="form-inline" method="get">
          <div class="row">
            <div class="col-md-4">
              <input type="hidden" name="view" value="productavailability">
              <input type="text" name="product" class="form-control" placeholder="Buscar por nombre o código..." value="<?php echo isset($_GET['product']) ? $_GET['product'] : ''; ?>">
            </div>
            <?php if($curr_user && $curr_user->kind == 1): ?>
            <div class="col-md-4">
              <select name="branch_id" class="form-control">
                <option value="">Todas las sucursales</option>
                <?php foreach(BranchData::getAllActive() as $branch): ?>
                  <option value="<?php echo $branch->id; ?>" <?php echo (isset($_GET['branch_id']) && $_GET['branch_id'] == $branch->id) ? 'selected' : ''; ?>><?php echo $branch->name; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endif; ?>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body p-0">
        <?php
        $products = array();
        if(isset($_GET['product']) && $_GET['product'] != ''){
          $products = ProductData::getLike($_GET['product']);
        } else {
          $products = ProductData::getAll();
        }
        
        // Si es vendedor (kind=1) y selecciona sucursal, filtra por esa sucursal
              $branch_id_filter = null;
        if($curr_user && $curr_user->kind == 1 && isset($_GET['branch_id']) && $_GET['branch_id'] != ''){
          $branch_id_filter = $_GET['branch_id'];
        }
        
        if(count($products)>0){
          foreach($products as $product){
            $available = OperationData::getQYesF($product->id, $branch_id_filter) > 0;
        ?>
          <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <div>
              <h6 class="mb-0"><?php echo $product->name; ?></h6>
              <small class="text-muted">Código: <?php echo $product->barcode; ?></small>
            </div>
            <div>
              <?php if($available): ?>
                <span class="badge bg-success">Disponible</span>
              <?php else: ?>
                <span class="badge bg-danger">Agotado</span>
              <?php endif; ?>
            </div>
          </div>
        <?php
          }
        } else {
        ?>
          <div class="p-5 text-center text-muted">
            <i class="bi bi-search" style="font-size: 3rem;"></i>
            <p class="mt-2">No se encontraron productos.</p>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>