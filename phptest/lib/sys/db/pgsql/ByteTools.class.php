<?php

class sys_db_pgsql_ByteTools {
	public function __construct(){}
	static function setInt32($byte, $pos, $val) {
		$byte->b[$pos] = chr($val >> 24);
		$byte->b[$pos + 1] = chr($val >> 16);
		$byte->b[$pos + 2] = chr($val >> 8);
		$byte->b[$pos + 3] = chr($val);
	}
	function __toString() { return 'sys.db.pgsql.ByteTools'; }
}
