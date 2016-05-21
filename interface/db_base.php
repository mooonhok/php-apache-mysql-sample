<?php
//by HANK  www.teewho.cn
/**
[insert demo:]
$arr = array(‘user_name’ => ‘hank’ , ‘password’ => ‘123456’ , ‘salt’ => ‘12333’);
$uid = teewho_db::insert(‘user’, $arr, true);
[four methods of select]
fetch_data()获取二维数组
fetch_row()获取一维数组
fetch_itme()获取一个键值
count()获取count
*/
include_once("saemysql.class.php");

class DB {
	var $errocode;
	var $erromsg;

	function errormsg(){
		$o = & self::in();
		return $o->errmsg();
	}

	function errorno(){
		$o = & self::in();
		return $o->errno();
	}


	function getdata ($arr , $separator = ',') {
		$str = $s = '';
		foreach ((array)$arr as $k => $v) {
			if( 'string' == gettype( $v)){
				$v = self::in()->escape( $v);
			}
			$str .= $s."`{$k}`='{$v}'";
			$s = $separator;
		}
		return $str;
	}

	function count ($tb , $fields = '*' , $terms = ''){
		$o = & self::in();
		$where = empty($terms) ? '1' : $terms;
		$query = "select count({$fields}) from `{$tb}` where  {$where}";
		$num = $o->getVar($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $num?$num:0;
	}

	function fetch_data ($tb , $fields = '*' , $terms = ''){
		$o = & self::in();
		$query = "select {$fields} from `{$tb}` {$terms}";
		$attr = $o->getData($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $attr?$attr:array();
	}

	function fetch_row ($tb , $fields = '*' , $terms = ''){
		$o = & self::in();
		$data = array();
		$query = "select {$fields} from `{$tb}` {$terms}";
		$line = $o->getLine($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $line;
	}

	function fetch_item ($tb , $field , $terms = ''){
		$o = & self::in();
		$data = array();
		$query = "select {$field} from `{$tb}` {$terms}";
		$item = $o->getVar($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $item?$item:array();
	}
	function fetch_item_array($tb,$item,$terms=''){
		$o = & self::in();
		$data = array();
		$query = "select {$item} from `{$tb}` {$terms}";
		$attr = $o->getData($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		if($attr == null)
			return array();
		$num = count($attr);
		$array = array();
		for($i=0;$i<$num;$i++)
		{
			$array[$i] = $attr[$i][$item];
		}
		return $array;
	}
	function insert($tb, $arr,  $getinsertid = false, $replace = false) {
		$o = & self::in();
		$data = self::getdata($arr);
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$query = "{$cmd} `{$tb}` SET {$data}";
       	$return = $o->runSql($query);
       	
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $getinsertid ? $o->lastId() : $return;
	}

	function insert_id() {
		$o = & self::in();
		$id = $o->lastId();
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $id;
	}

	function update($tb, $arr,  $terms = NULL , $getarows = false , $low_priority = false) {
		$o = & self::in();
		$data = self::getdata($arr);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$where = empty($terms) ? '1' : $terms;
		$query = "{$cmd} `{$tb}` SET {$data} WHERE {$where}";
		$return = $o->runSql($query);
		if( $o->errno() != 0 )
		{
			 die("Error:".$o->errmsg());
			return FALSE;
		}
		return $getarows ? $o->affectedRows() : 1;
	}

	function delete($tb, $terms = NULL,$getarows = false, $limit = 0) {
		$o = & self::in();
		$where = empty($terms) ? '1' : $terms;
		$query = "DELETE FROM `{$tb}` WHERE {$where} ".($limit ? "LIMIT {$limit}" : '');
		$return = $o->runSql($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $getarows ? $o->affectedRows() : $return;
	}

	function affected_rows() {
		$o = & self::in();
		$rows = $o->affectedRows();
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $rows?$rows:0;
	}


	function query($query) {
		$o = & self::in();
		$q = $o->runSql($query);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $q?$q:array();
	}
	function fetch_array($sql){
		$o = & self::in();
		$attr = $o->getData($sql);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $attr?$attr:array();
	}
	function fetch_line($sql){
		$o = & self::in();
		$line = $o->getLine($sql);
		if( $o->errno() != 0 )
		{
			 // die("Error:".$o->errmsg());
			return FALSE;
		}
		return $line?$line:array();
	}
	function &in() {
		static $object;
		if(empty($object)) {
			$object = new SaeMysql();
		}
		return $object;
	}

}