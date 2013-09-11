package sys.db;
import sys.db.Postgres;
class TestPostgres extends haxe.unit.TestCase {
	var con : Connection;
	override public function setup(){
		con = Postgres.connect({
			host     : "localhost",
			user     : "jdonaldson",
			pass     : "jdonaldson",
			database : "scratch"
		});

		con.request('
				CREATE TABLE Persons
				(
				 PersonID int,
				 LastName varchar(255),
				 FirstName varchar(255),
				 Address varchar(255),
				 City varchar(255)
				)
				');

	}

	override public function tearDown(){
		con.request("drop table Persons");
		con.close();
	}

	public function testDbSanity() {
		var error = false;
		assertEquals(con.dbName(), "PostgreSQL");	
	}

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

	public function testTimeParse(){
		var time = Date.now().getTime();
		var res = con.request('SELECT NOW() AS "theTime"');
		assertEquals(res.length, 1);
		var res_date : { theTime : Date} = untyped res.next();
		var res_time = res_date.theTime.getTime();
		assertEquals(time, res_time);
	}
	
}
