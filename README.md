# Postgrehx

Postgrehx is a pure Haxe implementation of the [Postgres wire
protocol](http://www.postgresql.org/docs/9.2/static/protocol.html).

This library is in an early alpha state, but is passing some basic tests.

[![Build Status](https://travis-ci.org/jdonaldson/postgrehx.png)](https://travis-ci.org/jdonaldson/postgrehx)

Here's the current TODO:

1. automatic decoding of types other than numbers, booleans, strings, 
   and timestamps.
2. fill out the rest of the haxe sys.db.Connection interface
3. properly handle the async message flow from postgres 

DONE:
1. MD5 and basic authentication (thanks Juraj!)

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

## Running Tests

If you want to run the tests, execute the tools/create_db.sh bash script that
contains the database, pass, and test user for the tests.  To get rid of it,
run the tools/destroy_db.sh script.

Postgrehx should work with most modern versions of PGSQL (version 8 or
later). The tests are intended to run against  version 9.3 or later, as they
test the latest json indexing methods.

## Acknowledgments

Thanks to [theRemix](https://github.com/theremix) and
[back2Dos](https://github.com/back2dos) for contributing test cases
and fixes.


