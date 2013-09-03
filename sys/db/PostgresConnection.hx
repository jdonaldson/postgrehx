private class PostgresConnection implements Connection {
	public function request( s : String ) : ResultSet {}
	public function close() : Void {}
	public function escape ( s : String ) : String {}
	public function quote( s : String ) : String {}
	public function addValue( s : StringBuf, v : Dynamic ) : Void {}
	public function lastInsertId() : Int {}
	public function dbName() : String {
		return "Postgres";
	}
	public function startTransaction() : Void {}
	public function commit() : Void {}
	public function rollback() : Void {}

}
