package sys.db;
import sys.db.Types;
import sys.db.Connection;
import haxe.unit.TestCase;

class TestSpodObject extends sys.db.Object{
    public var id: SUId;
    public var name: SString<255>;
    public var date: SDate;
    public var is_active: SBool;

}
class TestSpodPostgres extends TestCase{
    static var con : Connection;
    public function new() {
        super();
        con = TestBase.setup();
    }

    public function testSPODManagerTest() {
        sys.db.Manager.cnx  = con;
        con.request('
                CREATE TABLE TestSpodObject (
                    id SERIAL NOT NULL,
                    name character varying(255),
                    date timestamp without time zone,
                    is_active bool
                    );
                ');
        var test_spod_object:TestSpodObject = new TestSpodObject();
        test_spod_object.name = "test";
        test_spod_object.date = Date.now();
        test_spod_object.is_active = true;
        test_spod_object.insert();
        assertTrue(test_spod_object.id != null);
        var test_spod_manager:Manager<TestSpodObject> = new Manager<TestSpodObject>(TestSpodObject);
        var count = test_spod_manager.count($name == test_spod_object.name);
        assertEquals(count, 1);

        var get_spod_object:TestSpodObject = test_spod_manager.get(test_spod_object.id);
        assertTrue(get_spod_object != null);
        assertTrue(get_spod_object.name == "test");
    }
}
