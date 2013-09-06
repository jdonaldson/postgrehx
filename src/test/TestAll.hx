import sys.db.TestPostgres;
import haxe.unit.TestRunner;
class TestAll {
	static function main() {
		var r = new TestRunner();
		r.add(new TestPostgres());
		r.run();
	}
}
