<?php

class sys_db_PostgresResultSet implements sys_db_ResultSet{
	public function __construct($field_descriptions = null, $data_iterator = null) {
		if(!php_Boot::$skip_constructor) {
		if($field_descriptions === null) {
			$field_descriptions = new _hx_array(array());
		}
		if($data_iterator === null) {
			$data_iterator = _hx_deref(new _hx_array(array()))->iterator();
		}
		$this->data_iterator = $data_iterator;
		$this->field_descriptions = $field_descriptions;
	}}
	public function results() {
		return Lambda::hlist(sys_db_PostgresResultSet_0($this));
	}
	public function next() {
		$this->current_row = $this->data_iterator->next();
		$obj = _hx_anonymous(array());
		{
			$_g1 = 0; $_g = $this->field_descriptions->length;
			while($_g1 < $_g) {
				$idx = $_g1++;
				$field_desc = $this->field_descriptions[$idx];
				$obj->{$field_desc->name} = sys_db_PostgresResultSet::readType($field_desc->datatype_object_id, $this->current_row[$idx]);
				unset($idx,$field_desc);
			}
		}
		$this->row_count += 1;
		return $obj;
	}
	public function hasNext() {
		return $this->data_iterator->hasNext();
	}
	public function getResult($col_idx) {
		return _hx_array_get($this->current_row, $col_idx)->toString();
	}
	public function getIntResult($col_idx) {
		$bytes = $this->current_row[$col_idx];
		return Std::parseInt($bytes->toString());
	}
	public function getFloatResult($col_idx) {
		$bytes = $this->current_row[$col_idx];
		return Std::parseFloat($bytes->toString());
	}
	public function getFieldsNames() {
		return sys_db_PostgresResultSet_1($this);
	}
	public function get_nfields() {
		return $this->field_descriptions->length;
	}
	public function get_length() {
		if($this->set_length === null) {
			$this->cached_rows = sys_db_PostgresResultSet_2($this);
			$this->set_length = $this->cached_rows->length + $this->row_count;
			$this->data_iterator = $this->cached_rows->iterator();
		}
		return $this->set_length;
	}
	public $nfields;
	public $length;
	public $current_row = null;
	public $set_length = 0;
	public $row_count = 0;
	public $cached_rows;
	public $field_descriptions;
	public $data_iterator;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static function parseTimeStampTz($stamp) {
		$tz = _hx_substring($stamp, strlen($stamp) - 3, strlen($stamp));
		$offset = Std::parseInt($tz);
		$date = Date::fromString(_hx_array_get(_hx_explode(".", $stamp), 0));
		return $date;
	}
	static function readType($oid, $bytes) {
		if($bytes === null) {
			return null;
		}
		$string = $bytes->toString();
		return sys_db_PostgresResultSet_3($bytes, $oid, $string);
	}
	static $__properties__ = array("get_length" => "get_length","get_nfields" => "get_nfields");
	function __toString() { return 'sys.db.PostgresResultSet'; }
}
function sys_db_PostgresResultSet_0(&$__hx__this) {
	{
		$_g = new _hx_array(array());
		$__hx__it = $__hx__this;
		while($__hx__it->hasNext()) {
			$f = $__hx__it->next();
			$_g->push($f);
		}
		return $_g;
	}
}
function sys_db_PostgresResultSet_1(&$__hx__this) {
	{
		$_g = new _hx_array(array());
		{
			$_g1 = 0; $_g2 = $__hx__this->field_descriptions;
			while($_g1 < $_g2->length) {
				$f = $_g2[$_g1];
				++$_g1;
				$_g->push($f->name);
				unset($f);
			}
		}
		return $_g;
	}
}
function sys_db_PostgresResultSet_2(&$__hx__this) {
	{
		$_g = new _hx_array(array());
		$__hx__it = $__hx__this->data_iterator;
		while($__hx__it->hasNext()) {
			$row = $__hx__it->next();
			$_g->push($row);
		}
		return $_g;
	}
}
function sys_db_PostgresResultSet_3(&$bytes, &$oid, &$string) {
	switch($oid) {
	case sys_db_pgsql__DataType_DataType_Impl_::$oidINT8:{
		return Std::parseInt($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidINT4:{
		return Std::parseInt($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidINT2:{
		return Std::parseInt($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidBOOL:{
		return Std::parseInt($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidFLOAT4:{
		return Std::parseFloat($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidFLOAT8:{
		return Std::parseFloat($string);
	}break;
	case sys_db_pgsql__DataType_DataType_Impl_::$oidTIMESTAMPTZ:{
		return sys_db_PostgresResultSet::parseTimeStampTz($string);
	}break;
	default:{
		return $string;
	}break;
	}
}
