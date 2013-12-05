<?php

class PHPTest {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->setup();
	}}
	public function testRawNullValueInsert() {
		$this->con->request("\x0A                CREATE TABLE rawnullvaluetest (\x0A                    id integer NOT NULL,\x0A                    name character varying(255),\x0A                    date timestamp without time zone\x0A                    );\x0A                ");
		$this->con->request("INSERT INTO rawnullvaluetest VALUES (1, 'foo', NULL);");
		$res = $this->con->request("\x0A                SELECT * FROM test_haxe_schema.rawnullvaluetest\x0A                ");
		$r = $res->results()->first();
		haxe_Log::trace("r.id != null : " . Std::string(_hx_field($r, "id") !== null), _hx_anonymous(array("fileName" => "PHPTest.hx", "lineNumber" => 76, "className" => "PHPTest", "methodName" => "testRawNullValueInsert")));
		haxe_Log::trace("r.date == null : " . Std::string(_hx_field($r, "date") === null), _hx_anonymous(array("fileName" => "PHPTest.hx", "lineNumber" => 77, "className" => "PHPTest", "methodName" => "testRawNullValueInsert")));
	}
	public function testNullValue() {
		$foo = _hx_anonymous(array("Present" => "bar", "Missing" => null));
		$this->con->request("\x0A                CREATE TABLE \"NullValueTest\" (\x0A                    \"Present\" varchar(255),\x0A                    \"Missing\" varchar(255)\x0A                    );\x0A                ");
		$this->con->request("\x0A                INSERT INTO \"NullValueTest\" Values(\x0A                    " . _hx_string_or_null($this->con->quote($foo->Present)) . ",\x0A                    " . _hx_string_or_null($this->con->quote($foo->Missing)) . "\x0A                    );\x0A                ");
		$res = $this->con->request("\x0A                SELECT * FROM \"NullValueTest\"\x0A                ");
		$ret_foo = $res->results()->first();
		haxe_Log::trace("ret_foo.Missing == null : " . Std::string($ret_foo->Missing === null), _hx_anonymous(array("fileName" => "PHPTest.hx", "lineNumber" => 56, "className" => "PHPTest", "methodName" => "testNullValue")));
	}
	public function setup() {
		$set_user = "test_haxe_user";
		$set_pass = "test_haxe_pass";
		if(Sys::getEnv("TRAVIS") === "true") {
			$set_user = "postgres";
			$set_pass = "";
		}
		$this->con = sys_db_Postgres::connect(_hx_anonymous(array("host" => "localhost", "user" => $set_user, "database" => "test_haxe_db", "pass" => $set_pass)));
	}
	public $con;
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
	static $user = "test_haxe_user";
	static $pass = "test_haxe_pass";
	static $db = "test_haxe_db";
	static $schema = "test_haxe_schema";
	static function main() {
		$initcon = sys_db_Postgres::connect(_hx_anonymous(array("host" => "localhost", "user" => "test_haxe_user", "database" => "test_haxe_db", "pass" => "test_haxe_pass")));
		$initcon->request("drop schema if exists " . "test_haxe_schema" . " cascade");
		$initcon->request("create schema " . "test_haxe_schema");
		(isset($initcon->close) ? $initcon->close: array($initcon, "close"));
		$php_test = new PHPTest();
		$php_test->testNullValue();
		$php_test->testRawNullValueInsert();
	}
	function __toString() { return 'PHPTest'; }
}
