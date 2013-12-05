<?php

class sys_db_pgsql_ClientMessageType extends Enum {
	public static function PasswordMessage($s) { return new sys_db_pgsql_ClientMessageType("PasswordMessage", 1, array($s)); }
	public static function Query($query) { return new sys_db_pgsql_ClientMessageType("Query", 2, array($query)); }
	public static function StartupMessage($args) { return new sys_db_pgsql_ClientMessageType("StartupMessage", 0, array($args)); }
	public static $__constructors = array(1 => 'PasswordMessage', 2 => 'Query', 0 => 'StartupMessage');
	}
