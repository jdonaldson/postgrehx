package sys.db;
import sys.db.Postgres;
import sys.db.pgsql.Error;
import haxe.unit.TestCase;

class TestPostgres extends TestCase {
    inline static var user = "test_haxe_pgsql_user";
    inline static var pass = "test_haxe_pgsql_pass";
    inline static var db   = "test_haxe_pgsql";

	var con : sys.db.Connection;

	/**
	  Nuke the default schema every time the test is run
	 **/
	public static function __init__(){
	    // resetting the db on travis causes errors, and isn't necessary
	    if (Sys.getEnv("Travis") != "true") {
            var initcon = Postgres.connect({
                host	 : "localhost",
                user	 : user,
                database : db,
                pass	 : pass
            });
            initcon.request('drop schema if exists public cascade');
            initcon.request('create schema public');
            initcon.close;
        } 
	}

	override public function setup(){
	    var set_user = user;
	    var set_pass = pass;
	    if (Sys.getEnv("Travis") == "true"){
            set_user = "postgres";
            set_pass = "";
        }
        con = Postgres.connect({
            host	 : "localhost",
            user	 : set_user,
            database : db,
            pass	 : set_pass
        });
	}

	/**
	  Am I even who I say I am?
	 **/
	public function testDbSanity() assertEquals(con.dbName(), "PostgreSQL");


	public function testBasicTable() {
		var id = 12345;
		var person = {
			PersonID : id,
			LastName : "Bar",
			FirstName : "Foo",
			Address : "Somewhere",
			City : "Someplace",
		}

		con.request('
				CREATE TABLE "Persons" (
					"PersonID" int,
					"LastName" varchar(255),
					"FirstName" varchar(255),
					"Address" varchar(255),
					"City" varchar(255)
					); '
				);

		con.request('
				INSERT INTO "Persons" Values(
					${person.PersonID},
					${con.quote(person.LastName)},
					${con.quote(person.FirstName)},
					${con.quote(person.Address)},
					${con.quote(person.City)}
					)'
				);

		var res = con.request('
				SELECT * FROM "Persons" WHERE "PersonID" = $id
				');

		assertEquals(res.length, 1);
		assertEquals(Std.string(person), Std.string(res.next()));

	}

	/**
	  Basic test to ensure that a simple query works
	 **/
	public function testBasicQuery(){

		var res = con.request("
				SELECT table_schema,table_name
				FROM information_schema.tables
				ORDER BY table_schema,table_name;
				");
				assertTrue(res.length > 0);

		var obj = res.next();
		assertEquals(Reflect.fields(obj).length, 2);
		assertTrue(obj.table_schema != null);
		assertTrue(obj.table_name != null);
	}

	/**
		Test to ensure that date parsing works
	 **/
	public function testTimeParse(){
		var time = Date.now().getTime();
		var res = con.request('SELECT NOW() AS "theTime"');
		assertEquals(res.length, 1);
		var res_date : { theTime : Date} = untyped res.next();
		var res_time = res_date.theTime.getTime();
		assertEquals(time, res_time);
	}
}
