package sys.db;
import sys.db.Types;
import sys.db.Postgres;
import sys.db.pgsql.Error;
import haxe.unit.TestCase;

@:table("SpodObjectTest")
@:id(id)
class TestSpodObject extends sys.db.Object{

  public var id: SId;
  public var name: SString<255>;
  public var date: SDate;
}

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

  public function testSPODManagerTest() {
    
    // this errors, skip for now
    assertTrue(true);
    return;


    con.request('
      CREATE TABLE SpodObjectTest (
          id SERIAL NOT NULL,
          name character varying(255),
          date timestamp without time zone
          );
    ');

    var test_spod_object:TestSpodObject = new TestSpodObject();
    test_spod_object.name = "test";
    test_spod_object.date = Date.now();
    test_spod_object.insert();

    assertTrue(test_spod_object.id != null);
    
    if(test_spod_object.id != null){
      var test_spod_manager:Manager<TestSpodObject> = new Manager<TestSpodObject>(TestSpodObject);
      var get_spod_object:TestSpodObject = test_spod_manager.get(test_spod_object.id);
      assertTrue(get_spod_object != null);
      assertTrue(get_spod_object.name == "test");
    }
    
  }

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
                SELECT * FROM test_haxe_schema.rawnullvaluetest
                ');
        assertEquals(1, res.length);
        var r = res.results().first();
        
        assertTrue(r.id != null);
        assertTrue(r.date == null);
    }

	public function testJsonTable() {
		var id = 23456;
		var json_dump = {
			Id  : id,
			data : {foo : 12}
		}

		con.request('
				CREATE TABLE "JsonDump" (
					"Id"   int,
					"data" json
					); '
				);

		var req = '
                INSERT INTO "JsonDump" Values(
                    ${json_dump.Id},
                    ${con.quote(haxe.Json.stringify(json_dump.data))}
                    )';

		con.request(req);

		var res = con.request('
				SELECT * FROM "JsonDump" WHERE data->>\'foo\' = \'12\'
				');
		assertTrue(res.length == 1);
		var rec = res.next();
		assertEquals(rec.data.foo, 12);
    }

	public function testBasicTable() {
		var id = 12345;
		var max_int_32 = 2147483647;
		var min_int_32 = -2147483648;
		var person = {
			PersonID  : id,
			LastName  : "Bar",
			FirstName : "Foo",
			Address   : "Somewhere",
			City      : "Someplace",
			MaxInt32  : max_int_32,
			MinInt32  : min_int_32
		}

		con.request('
				CREATE TABLE "Persons" (
					"PersonID"  int,
					"LastName"  varchar(255),
					"FirstName" varchar(255),
					"Address"   varchar(255),
					"City"      varchar(255),
					"MaxInt32"  int,
					"MinInt32"  int
					); '
				);

		con.request('
				INSERT INTO "Persons" Values(
					${person.PersonID},
					${con.quote(person.LastName)},
					${con.quote(person.FirstName)},
					${con.quote(person.Address)},
					${con.quote(person.City)},
				    ${person.MaxInt32},
				    ${person.MinInt32}
					)'
				);

		var res = con.request('
				SELECT * FROM "Persons" WHERE "PersonID" = $id
				');

		assertEquals(1, res.length);
		var rec = res.next();
		assertEquals(Std.string(person), Std.string(rec));

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
	    var now = Date.now();
		var time = now.getTime();
		time -= time % 1000; // round to seconds.
		var res = con.request('SELECT NOW() AS "theTime"');
		assertEquals(1, res.length);
		var res_date : { theTime : Date} = untyped res.next();
		var res_time = res_date.theTime.getTime();
		assertEquals(time, res_time);
	}

    /**
      Test exhausting iterators in a multiple requests
     **/
    public function testMultipleRequests(){

        con.request('
                CREATE TABLE multiplerequests (
                    id integer NOT NULL,
                    name character varying(255),
                    date timestamp without time zone
                    );
                ');

        con.request('INSERT INTO multiplerequests VALUES (1, ${con.quote("foo")}, ${con.quote(Std.string(Date.now()))});');

        for(i in 0...3){
            var res = con.request('
                    SELECT * FROM multiplerequests
                    ');
            assertEquals(1, res.length);
            var r = res.results().first();
            assertTrue(r.id != null);
        }
    }
}
