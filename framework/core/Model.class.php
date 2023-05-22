<?php
class Model{
	protected $db;
	protected $table;
	protected $fields = array();

	public function __construct($table){
		$dbconfig['host'] = $GLOBALS['config']['host'];
		$dbconfig['user'] = $GLOBALS['config']['user'];
		$dbconfig['password'] = $GLOBALS['config']['password'];
		$dbconfig['dbname'] = $GLOBALS['config']['dbname'];
		$dbconfig['port'] = $GLOBALS['config']['port'];
		$dbconfig['charset'] = $GLOBALS['config']['charset'];
		
		$this->db = new Mysql($dbconfig);
		$this->table = $GLOBALS['config']['prefix'] . $table;
		$this->getFields();
	}

	private function getFields(){
		$sql = "DESC ". $this->table;
		$result = $this->db->getAll($sql);
		foreach ($result as $v) {
			$this->fields[] = $v['Field'];
			if ($v['Key'] == 'PRI') {
				$pk = $v['Field'];
			}
		}
		if (isset($pk)) {
			$this->fields['pk'] = $pk;
		}
	}

	public function insert($list){
		$field_list = '';
		$value_list = '';
		foreach ($list as $k => $v) {
			if (in_array($k, $this->fields)) {
				$field_list .= "`".$k."`" . ',';
				$value_list .= "'".$v."'" . ',';
			}
		}
		$field_list = rtrim($field_list,',');
		$value_list = rtrim($value_list,',');
		$sql = "INSERT INTO `{$this->table}` ({$field_list}) VALUES ($value_list)";
		if ($this->db->query($sql)) {
			return $this->db->getInsertId();
		} else {
			return false;
		}
		
	}

	public function update($list){
		$uplist = '';
		$where = 0;
		foreach ($list as $k => $v) {
			if (in_array($k, $this->fields)) {
				if ($k == $this->fields['pk']) {
					$where = "`$k`=$v";
				} else {
					$uplist .= "`$k`='$v'".",";
				}
			}
		}
		$uplist = rtrim($uplist,',');
		$sql = "UPDATE `{$this->table}` SET {$uplist} WHERE {$where}";
		if ($this->db->query($sql)) {
			if ($rows = mysql_affected_rows()) {
				return $rows;
			} else {
				return false;
			}	
		} else {
			return false;
		}
		
	}
	
	public function delete($pk){
		$where = 0;
		if (is_array($pk)) {
			$where = "`{$this->fields['pk']}` in (".implode(',', $pk).")";
		} else {
			$where = "`{$this->fields['pk']}`=$pk";
		}
		$sql = "DELETE FROM `{$this->table}` WHERE $where";
		if ($this->db->query($sql)) {
			if ($rows = mysql_affected_rows()) {
				return $rows;
			} else {
				return false;
			}		
		} else {
			return false;
		}
	}

	/**
	 * 通过主键获取信息
	 * @param $pk int 主键值
	 * @return array 单条记录
	 */
	public function selectByPk($pk){
		$sql = "select * from `{$this->table}` where `{$this->fields['pk']}`=$pk";
		return $this->db->getRow($sql);
	}

	/**
	 * 获取总的记录数
	 * @param string $where 查询条件，如"id=1"
	 * @return number 返回查询的记录数
	 */
	public function total($where){
		if(empty($where)){
			$sql = "select count(*) from {$this->table}";
		}else{
			$sql = "select count(*) from {$this->table} where $where";
		}
		return $this->db->getOne($sql);
	}

	/**
	 * 分页获取信息
	 * @param $offset int 偏移量
	 * @param $limit int 每次取记录的条数
	 * @param $where string where条件,默认为空
	 */
	public function pageRows($offset, $limit,$where = ''){
		if (empty($where)){
			$sql = "select * from {$this->table} limit $offset, $limit";
		} else {
			$sql = "select * from {$this->table}  where $where limit $offset, $limit";
		}
		
		return $this->db->getAll($sql);
	}

}
