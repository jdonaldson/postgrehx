<?php

class sys_db_Postgres {
	public function __construct(){}
	static function connect($params) {
		return new sys_db_PostgresConnection($params);
	}
	function __toString() { return 'sys.db.Postgres'; }
}
