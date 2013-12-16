import sys.db.TestPostgres;
import sys.db.TestSpodPostgres;
import haxe.unit.TestRunner;
class TestAll {
	static function main() {
		var r = new TestRunner();
		r.add(new TestPostgres());
		r.add(new TestSpodPostgres());
		r.run();
	}
}
