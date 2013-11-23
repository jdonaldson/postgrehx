package sys.db;
import sys.db.Postgres;
import sys.db.pgsql.Error;
import haxe.unit.TestCase;

class TestPostgres extends TestCase {

    inline static var user   = "test_haxe_user";
    inline static var pass   = "test_haxe_pass";
    inline static var db     = "test_haxe_db";
    inline static var schema = "test_haxe_schema";

	var con : sys.db.Connection;

	/**
	  Nuke the default schema every time the test is run
	 **/
    public static function __init__(){
        // resetting the db on travis causes errors, and isn't necessary
        if (Sys.getEnv("TRAVIS") != "true") {
            var initcon = Postgres.connect({
                host     : "localhost",
                user     : user,
                database : db,
                pass     : pass
            });
            initcon.request('drop schema if exists $schema cascade');
            initcon.request('create schema $schema');
            initcon.close;
        }
    }

	override public function setup(){
	    var set_user = user;
	    var set_pass = pass;

	    if (Sys.getEnv("TRAVIS") == "true"){
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


	public function testNullValue() {
	    var foo = {
            Present : "bar",
            Missing : null
        }
        con.request('
                CREATE TABLE "NullValueTest" (
                    "Present" varchar(255),
                    "Missing" varchar(255)
                    );
                ');

        con.request('
                INSERT INTO "NullValueTest" Values(
                    ${con.quote(foo.Present)},
                    ${con.quote(foo.Missing)}
                    );
                ');
        var res = con.request('
                SELECT * FROM "NullValueTest"
                ');

        assertEquals(1, res.length);
        var ret_foo : {Present:String, Missing:String} = res.results().first();
        assertTrue(ret_foo.Missing == null);
    }

	public function testRawNullValueInsert() {
        con.request('
                CREATE TABLE rawnullvaluetest (
                    id integer NOT NULL,
                    name character varying(255),
                    date timestamp without time zone
                    );
                ');

        con.request("INSERT INTO rawnullvaluetest VALUES (1, 'foo', NULL);");

        var res = con.request('
                SELECT * FROM rawnullvaluetest
                ');
        assertEquals(1, res.length);
        var r = res.results().first();
        assertTrue(r.id != null);
        assertTrue(r.date == null);
    }

	public function testBasicTable() {
		var id = 12345;
		var person = {
			PersonID  : id,
			LastName  : "Bar",
			FirstName : "Foo",
			Address   : "Somewhere",
			City      : "Someplace",
		}

		con.request('
				CREATE TABLE "Persons" (
					"PersonID"  int,
					"LastName"  varchar(255),
					"FirstName" varchar(255),
					"Address"   varchar(255),
					"City"      varchar(255)
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

		assertEquals(1, res.length);
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
		assertEquals(2, Reflect.fields(obj).length);
		assertTrue(obj.table_schema != null);
		assertTrue(obj.table_name != null);
	}

	/**
		Test to ensure that date parsing works
	 **/
	public function testTimeParse(){
		var time = Date.now().getTime();
		var res = con.request('SELECT NOW() AS "theTime"');
		assertEquals(1, res.length);
		var res_date : { theTime : Date} = untyped res.next();
		var res_time = res_date.theTime.getTime();
		assertEquals(time, res_time);
	}
}
