# Postgrehx

Postgrehx is a pure Haxe implementation of the [Postgres wire
protocol](http://www.postgresql.org/docs/9.2/static/protocol.html).

This library is in an early alpha state.  Here's the current TODO:

1. automatic decoding of types other than numbers, booleans, and strings.
2. fill out the rest of the haxe sys.db.Connection interface
3. properly handle the async message flow from postgres 

DONE:
1. MD5 and basic encryption (thanks Juraj!)

## Usage
```haxe
class Test {
	static function main() {
		var con = sys.db.Postgres.connect({
			host : "localhost",
			user : "jdonaldson",
			pass : "jdonaldson",
			database : "scratch"
		});
		trace(con.dbName() + " is the value for con.dbName()");
		// con.request('SELECT NOW() AS "theTime"');
		var res = con.request("
			SELECT table_schema,table_name
			FROM information_schema.tables
			ORDER BY table_schema,table_name;
			");
		for (r in res) trace(r);

		con.close();
		return;
	}
}
```
