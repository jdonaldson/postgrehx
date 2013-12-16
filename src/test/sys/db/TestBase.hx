package sys.db;
import sys.db.Connection;
class TestBase {
    inline static var user   = "test_haxe_user";
    inline static var pass   = "test_haxe_pass";
    inline static var db     = "test_haxe_db";
    inline static var schema = "test_haxe_schema";
    static var con : Connection;
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

	public static function setup() : Connection {
	    if (con != null) return con;
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
        return con;
	}

}
