<?php

/*
getQYesF() — de 3 queries a 1. Antes traía todas las filas a PHP y sumaba. Ahora MySQL hace la suma directamente con SUM(CASE WHEN ...). El resultado es el mismo número pero con un solo viaje a la BD.
getAllStocks() — método nuevo. Una sola query devuelve el stock de todos los productos agrupado por product_id. Las vistas de inventario, alertas y dashboard ahora llaman esto una vez al inicio y luego acceden al array con $stocks[$product->id] ?? 0 sin tocar la BD dentro del foreach.

Archivos modificados

core/app/model/OperationData.php
core/app/view/inventary-view.php
core/app/view/alerts-view.php
core/app/view/home-view.php
*/

class OperationData {
	public static $tablename = "operation";
	public $id;
	public $product_id;
	public $q;
	public $operation_type_id;
	public $sell_id;
	public $created_at;


	public $branch_id;

	public function __construct(){
		$this->product_id = "";
		$this->q = "";
		$this->operation_type_id = "";
		$this->branch_id = null;
		$this->created_at = "NOW()";
	}

	public function getBranch(){
		if($this->branch_id != null){
			return BranchData::getById($this->branch_id);
		}
		return null;
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (product_id,q,operation_type_id,sell_id,branch_id,created_at) ";
		$sql .= "values (\"$this->product_id\",\"$this->q\",$this->operation_type_id,".($this->sell_id ? $this->sell_id : "NULL").",".($this->branch_id ? $this->branch_id : "NULL").",$this->created_at)";
		return Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

// partiendo de que ya tenemos creado un objecto OperationData previamente utilizamos el contexto
	public function update(){
		$sql = "update ".self::$tablename." set product_id=\"$this->product_id\",q=\"$this->q\" where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new OperationData());
	}



	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());

	}



	public static function getAllByDateOfficial($start,$end,$branch_id=null){
 $sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\"";
		if($branch_id != null){ $sql .= " and branch_id=$branch_id"; }
		$sql .= " order by created_at desc";
		if($start == $end){
		 $sql = "select * from ".self::$tablename." where date(created_at) = \"$start\"";
		 if($branch_id != null){ $sql .= " and branch_id=$branch_id"; }
		 $sql .= " order by created_at desc";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

	public static function getAllByDateOfficialBP($product, $start,$end,$branch_id=null){
 $sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and product_id=$product";
		if($branch_id != null){ $sql .= " and branch_id=$branch_id"; }
		$sql .= " order by created_at desc";
		if($start == $end){
		 $sql = "select * from ".self::$tablename." where date(created_at) = \"$start\"";
		 if($branch_id != null){ $sql .= " and branch_id=$branch_id"; }
		 $sql .= " order by created_at desc";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

	public function getProduct(){ return ProductData::getById($this->product_id);}
	public function getOperationtype(){ return OperationTypeData::getById($this->operation_type_id);}





	public static function getQYesF($product_id, $branch_id = null){
		$sql = "SELECT COALESCE(SUM(CASE WHEN operation_type_id = 1 THEN q WHEN operation_type_id = 2 THEN -q ELSE 0 END), 0) as stock FROM ".self::$tablename." WHERE product_id=$product_id";
		if($branch_id != null){ $sql .= " AND branch_id=$branch_id"; }
		$query = Executor::doit($sql);
		$row = $query[0]->fetch_assoc();
		return $row ? floatval($row["stock"]) : 0;
	}

	// Devuelve stock de TODOS los productos en una sola query
	// Uso: $stocks = OperationData::getAllStocks();  $stock = $stocks[$product_id] ?? 0;
	public static function getAllStocks($branch_id = null){
		$sql = "SELECT product_id, COALESCE(SUM(CASE WHEN operation_type_id = 1 THEN q WHEN operation_type_id = 2 THEN -q ELSE 0 END), 0) as stock FROM ".self::$tablename;
		if($branch_id != null){ $sql .= " WHERE branch_id=$branch_id"; }
		$sql .= " GROUP BY product_id";
		$query = Executor::doit($sql);
		$stocks = array();
		while($row = $query[0]->fetch_assoc()){
			$stocks[$row["product_id"]] = floatval($row["stock"]);
		}
		return $stocks;
	}



	public static function getAllByProductIdCutId($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

	public static function getAllByProductId($product_id, $branch_id = null){
		$sql = "select * from ".self::$tablename." where product_id=$product_id ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}


	public static function getAllByProductIdCutIdOficial($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id order by created_at desc";
		return Model::many($query[0],new OperationData());
	}


	public static function getAllProductsBySellId($sell_id){
		$sql = "select * from ".self::$tablename." where sell_id=$sell_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}


	public static function getAllByProductIdCutIdYesF($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id order by created_at desc";
		return Model::many($query[0],new OperationData());
		return $array;
	}

////////////////////////////////////////////////////////////////////
	public static function getOutputQ($product_id,$cut_id){
		$q=0;
		$operations = self::getOutputByProductIdCutId($product_id,$cut_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach($operations as $operation){
			if($operation->operation_type_id==$input_id){ $q+=$operation->q; }
			else if($operation->operation_type_id==$output_id){  $q+=(-$operation->q); }
		}
		// print_r($data);
		return $q;
	}

	public static function getOutputQYesF($product_id){
		$q=0;
		$operations = self::getOutputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach($operations as $operation){
			if($operation->operation_type_id==$input_id){ $q+=$operation->q; }
			else if($operation->operation_type_id==$output_id){  $q+=(-$operation->q); }
		}
		// print_r($data);
		return $q;
	}

	public static function getInputQYesF($product_id){
		$q=0;
		$operations = self::getInputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		foreach($operations as $operation){
			if($operation->operation_type_id==$input_id){ $q+=$operation->q; }
		}
		// print_r($data);
		return $q;
	}



	public static function getOutputByProductIdCutId($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id and operation_type_id=2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}


	public static function getOutputByProductId($product_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and operation_type_id=2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

////////////////////////////////////////////////////////////////////
	public static function getInputQ($product_id,$cut_id){
		$q=0;
		return Model::many($query[0],new OperationData());
		$operations = self::getInputByProductId($product_id);
		$input_id = OperationTypeData::getByName("entrada")->id;
		$output_id = OperationTypeData::getByName("salida")->id;
		foreach($operations as $operation){
			if($operation->operation_type_id==$input_id){ $q+=$operation->q; }
			else if($operation->operation_type_id==$output_id){  $q+=(-$operation->q); }
		}
		// print_r($data);
		return $q;
	}


	public static function getInputByProductIdCutId($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

	public static function getInputByProductId($product_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

	public static function getInputByProductIdCutIdYesF($product_id,$cut_id){
		$sql = "select * from ".self::$tablename." where product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new OperationData());
	}

////////////////////////////////////////////////////////////////////////////


}

?>