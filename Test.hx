
class Test {
	static function main() {
		var con = sys.db.Postgres.connect({
			host : "localhost",
			user : "jdonaldson",
			pass : "jdonaldson",
			database : "scratch"
		});
		trace(Type.allEnums(sys.db.pgsql.Messages.ServerMessage));
		trace(con.dbName() + " is the value for con.dbName()");
		// con.request('SELECT NOW() AS "theTime"');
		var res = con.request("
			SELECT table_schema,table_name
			FROM information_schema.tables
			ORDER BY table_schema,table_name;
			");
		for (r in res) trace(r);
		var res2 = con.request('SELECT NOW() AS "theTime"');
		for (r in res2) trace(r);
		con.close();
		return;
	}
}
