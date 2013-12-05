<?php

class sys_db_CommandComplete extends Enum {
	public static function CommandComplete($tag) { return new sys_db_CommandComplete("CommandComplete", 1, array($tag)); }
	public static function DataRows($row_description, $data_rows) { return new sys_db_CommandComplete("DataRows", 2, array($row_description, $data_rows)); }
	public static $EmptyQueryResponse;
	public static $__constructors = array(1 => 'CommandComplete', 2 => 'DataRows', 0 => 'EmptyQueryResponse');
	}
sys_db_CommandComplete::$EmptyQueryResponse = new sys_db_CommandComplete("EmptyQueryResponse", 0);
