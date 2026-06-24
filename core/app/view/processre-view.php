<?php
if(isset($_SESSION["reabastecer"])){
	$cart = $_SESSION["reabastecer"];
	if(count($cart)>0){

		$process = true;

		if($process==true){
			$sell = new SellData();
			$sell->user_id = $_SESSION["user_id"];
			$sell->branch_id = !empty($_POST["branch_id"]) ? $_POST["branch_id"] : "NULL";
			
			if(isset($_POST["client_id"]) && $_POST["client_id"]!=""){
				$sell->person_id=$_POST["client_id"];
				$s = $sell->add_re_with_client();
			}else{
				$s = $sell->add_re();
			}

			foreach($cart as $c){
				$op = new OperationData();
				$op->product_id = $c["product_id"] ;
				$op->operation_type_id=1; // 1 - entrada
				$op->sell_id=$s[1];
				$op->q= $c["q"];
				$op->branch_id = !empty($_POST["branch_id"]) ? $_POST["branch_id"] : "NULL";
				$add = $op->add();			 		
			}
			
			unset($_SESSION["reabastecer"]);
			$_SESSION["success"] = "Compra (Reposición) procesada correctamente";
			print "<script>window.location='index.php?view=onere&id=$s[1]';</script>";
		}
	}
}
?>
