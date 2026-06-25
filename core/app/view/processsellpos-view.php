<?php

/* 24/06/2026 - Lo que hace el cambio: antes el sistema hacía cada INSERT de forma independiente sin importar si los demás salían bien. Ahora con begin_transaction() le dice a MySQL "guarda todo esto en espera", y solo con commit() al final lo confirma todo junto. Si cualquier INSERT falla en el medio, rollback() borra todo lo que se había guardado en espera y la BD queda intacta. En tu caso del día a día con XAMPP local casi nunca va a fallar, pero en producción con hosting compartido o con múltiples usuarios simultáneos sí puede pasar, y cuando pasa sin transacciones el daño es silencioso y difícil de detectar.*/



if(isset($_SESSION["cart"])){
	$cart = $_SESSION["cart"];
	if(count($cart)>0){
		$num_succ = 0;
		$process=false;
		$errors = array();
		foreach($cart as $c){
			$q = OperationData::getQYesF($c["product_id"]);
			if($c["q"]<=$q){
                $num_succ++;
			}else{
				$error = array("product_id"=>$c["product_id"],"message"=>"No hay suficiente cantidad de producto en inventario.");
				$errors[count($errors)] = $error;
			}
		}

        if($num_succ==count($cart)){
            $process = true;
        }

        if($process==false){
            $error_msg = "";
            foreach($errors as $e){
                $error_msg .= $e["message"] . " ";
            }
            $_SESSION["error"] = $error_msg;
            print "<script>window.location='index.php?view=sellpos';</script>";
        }

if($process==true){
			$con = Database::getCon();
			$con->begin_transaction();
			try {
				$curr_user = UserData::getById($_SESSION["user_id"]);
				$sell = new SellData();
				$sell->user_id = $_SESSION["user_id"];
				$sell->total = $_POST["total"];
				$sell->discount = $_POST["discount"];
				$sell->branch_id = $curr_user->branch_id;
				if(isset($_POST["client_id"]) && $_POST["client_id"]!=""){
					$sell->person_id=$_POST["client_id"];
					$s = $sell->add_with_client();
				}else{
					$s = $sell->add();
				}

				foreach($cart as $c){
					$op = new OperationData();
					$op->product_id = $c["product_id"];
					$op->operation_type_id = OperationTypeData::getByName("salida")->id;
					$op->sell_id = $s[1];
					$op->q = $c["q"];
					$op->branch_id = $curr_user->branch_id;
					$op->add();
				}

				$con->commit();
				unset($_SESSION["cart"]);
				$_SESSION["success"] = "Venta POS procesada correctamente";
				print "<script>window.location='index.php?view=onesell&id=$s[1]';</script>";

			} catch(Exception $e){
				$con->rollback();
				$_SESSION["error"] = "Error al procesar la venta. Por favor intente nuevamente.";
				print "<script>window.location='index.php?view=sellpos';</script>";
			}
		}
	}
}
?>
