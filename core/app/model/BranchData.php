<?php
class BranchData {
	public static $tablename = "branch";
	public $id;
	public $name;
	public $address;
	public $phone;
	public $is_active;
	public $created_at;

	public function __construct(){
		$this->name = "";
		$this->address = "";
		$this->phone = "";
		$this->is_active = 1;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,address,phone,is_active,created_at) ";
		$sql .= "values (\"$this->name\",\"$this->address\",\"$this->phone\",$this->is_active,$this->created_at)";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "delete from ".self::$tablename." where id=$id";
		Executor::doit($sql);
	}

	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",address=\"$this->address\",phone=\"$this->phone\",is_active=$this->is_active where id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		$found = null;
		$data = new BranchData();
		while($r = $query[0]->fetch_array()){
			$data->id = $r['id'];
			$data->name = $r['name'];
			$data->address = $r['address'];
			$data->phone = $r['phone'];
			$data->is_active = $r['is_active'];
			$data->created_at = $r['created_at'];
			$found = $data;
			break;
		}
		return $found;
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename." order by created_at desc";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new BranchData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->address = $r['address'];
			$array[$cnt]->phone = $r['phone'];
			$array[$cnt]->is_active = $r['is_active'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}

	public static function getAllActive(){
		$sql = "select * from ".self::$tablename." where is_active=1 order by name asc";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new BranchData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->address = $r['address'];
			$array[$cnt]->phone = $r['phone'];
			$array[$cnt]->is_active = $r['is_active'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}

	public static function getLike($q){
		$sql = "select * from ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new BranchData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->address = $r['address'];
			$array[$cnt]->phone = $r['phone'];
			$array[$cnt]->is_active = $r['is_active'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}
}
?>
