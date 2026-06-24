<?php
header('Content-Type: application/json');

if(isset($_POST["barcode"]) && $_POST["barcode"] != "") {
    $barcode = $_POST["barcode"];
    $product = ProductData::getByBarcode($barcode);
    
    if($product != null && $product->is_active) {
        $product_id = $product->id;
        $q_to_add = 1;
        $user = UserData::getById($_SESSION["user_id"]);
        $branch_id = $user ? $user->branch_id : null;
        $q_inventory = OperationData::getQYesF($product_id, $branch_id);
        
        $cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : array();
        $found = false;
        $index = 0;
        $q_in_cart = 0;
        
        foreach($cart as $c){
            if($c["product_id"] == $product_id){
                $found = true;
                $q_in_cart = $c["q"];
                break;
            }
            $index++;
        }
        
        if(($q_in_cart + $q_to_add) <= $q_inventory){
            if($found){
                $cart[$index]["q"] += $q_to_add;
            } else {
                $cart[] = array("product_id" => $product_id, "q" => $q_to_add);
            }
            $_SESSION["cart"] = $cart;
            echo json_encode(array("status" => "success", "name" => $product->name, "product_id" => $product_id));
        } else {
            echo json_encode(array("status" => "error_insufficient_stock", "name" => $product->name));
        }
    } else {
        echo json_encode(array("status" => "not_found"));
    }
} else {
    echo json_encode(array("status" => "invalid_request"));
}
?>
