<?php
class SellData {
	public static $tablename = "sell";
	public $id;
	public $person_id;
	public $user_id;
	public $operation_type_id;
	public $box_id;
	public $total;
	public $cash;
	public $discount;
	public $branch_id;
	public $created_at;
	public $date;


	public function __construct(){
		$this->created_at = "NOW()";
		$this->branch_id = null;
	}

	public function getPerson(){ return PersonData::getById($this->person_id);}
	public function getUser(){ return UserData::getById($this->user_id);}
	public function getBranch(){
		if($this->branch_id != null){
			return BranchData::getById($this->branch_id);
		}
		return null;
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (total,discount,user_id,branch_id,created_at) ";
		$sql .= "values ($this->total,$this->discount,$this->user_id,".($this->branch_id ? $this->branch_id : "NULL").",$this->created_at)";
		return Executor::doit($sql);
	}

	public function add_re(){
		$sql = "insert into ".self::$tablename." (user_id,operation_type_id,branch_id,created_at) ";
		$sql .= "values ($this->user_id,1,".($this->branch_id ? $this->branch_id : "NULL").",$this->created_at)";
		return Executor::doit($sql);
	}


	public function add_with_client(){
		$sql = "insert into ".self::$tablename." (total,discount,person_id,user_id,branch_id,created_at) ";
		$sql .= "values ($this->total,$this->discount,$this->person_id,$this->user_id,".($this->branch_id ? $this->branch_id : "NULL").",$this->created_at)";
		return Executor::doit($sql);
	}

	public function add_re_with_client(){
		$sql = "insert into ".self::$tablename." (person_id,operation_type_id,user_id,branch_id,created_at) ";
		$sql .= "values ($this->person_id,1,$this->user_id,".($this->branch_id ? $this->branch_id : "NULL").",$this->created_at)";
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

	public function update_box(){
		$sql = "update ".self::$tablename." set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		 $sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new SellData());
	}



	public static function getSells($branch_id = null){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getSellsUnBoxed($branch_id = null){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 and box_id is NULL ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getByBoxId($id){
		$sql = "select * from ".self::$tablename." where operation_type_id=2 and box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getRes($branch_id = null){
		$sql = "select * from ".self::$tablename." where operation_type_id=1 ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "select * from ".self::$tablename." where id<=$start_from limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	public static function getAllByDateOp($start,$end,$op, $branch_id = null){
		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and operation_type_id=$op ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}
	public static function getAllByDateBCOp($clientid,$start,$end,$op, $branch_id = null){
		$sql = "select * from ".self::$tablename." where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and client_id=$clientid and operation_type_id=$op ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());

	}

	public static function getSellsLast30Days($branch_id = null){
		$sql = "select date(created_at) as date, sum(total) as total from ".self::$tablename." where operation_type_id=2 ";
		if($branch_id != null){
			$sql .= " and branch_id=$branch_id ";
		}
		$sql .= " and created_at >= date_sub(now(), interval 30 day) group by date(created_at) order by date(created_at) asc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SellData());
	}

}

?>