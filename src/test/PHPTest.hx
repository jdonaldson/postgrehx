import sys.db.Postgres;

class PHPTest {

    inline static var user   = "test_haxe_user";
    inline static var pass   = "test_haxe_pass";
    inline static var db     = "test_haxe_db";
    inline static var schema = "test_haxe_schema";

  var con : sys.db.Connection;

  public function new(){
    setup();
  }

  private function setup(){
      var set_user = user;
      var set_pass = pass;

      if (Sys.getEnv("TRAVIS") == "true"){
            set_user = "postgres";
            set_pass = "";
        }
        con = Postgres.connect({
            host   : "localhost",
            user   : set_user,
            database : db,
            pass   : set_pass
        });
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

        var ret_foo : {Present:String, Missing:String} = res.results().first();

        trace("ret_foo.Missing == null : " + (ret_foo.Missing == null));
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
        
        var r = res.results().first();
        
        trace("r.id != null : " + (r.id != null));
        trace("r.date == null : " + (r.date == null));
    }

  static function main() {
    // init, clear db
    var initcon = Postgres.connect({
                host     : "localhost",
                user     : user,
                database : db,
                pass     : pass
            });
    initcon.request('drop schema if exists $schema cascade');
    initcon.request('create schema $schema');
    initcon.close;

    var php_test = new PHPTest();

    php_test.testNullValue();
    php_test.testRawNullValueInsert();
  }
}
