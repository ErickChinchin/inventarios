<?php
header('Content-Type: application/json');

if(isset($_POST["barcode"]) && $_POST["barcode"] != "") {
    $barcode = $_POST["barcode"];
    $product = ProductData::getByBarcode($barcode);
    
    if($product != null && $product->is_active) {
        $product_id = $product->id;
        $q_to_add = 1;
        
        $cart = isset($_SESSION["reabastecer"]) ? $_SESSION["reabastecer"] : array();
        $found = false;
        $index = 0;
        
        foreach($cart as $c){
            if($c["product_id"] == $product_id){
                $found = true;
                break;
            }
            $index++;
        }
        
        if($found){
            $cart[$index]["q"] += $q_to_add;
        } else {
            $cart[] = array("product_id" => $product_id, "q" => $q_to_add);
        }
        $_SESSION["reabastecer"] = $cart;
        echo json_encode(array("status" => "success", "name" => $product->name, "product_id" => $product_id));
    } else {
        echo json_encode(array("status" => "not_found"));
    }
} else {
    echo json_encode(array("status" => "invalid_request"));
}
?>
