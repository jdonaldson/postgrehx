package sys.db;

import sys.net.Host;
import haxe.io.Input;
import haxe.io.BytesOutput;
import haxe.io.BytesInput;
import sys.net.Socket;
import haxe.io.BytesBuffer;
import haxe.io.Bytes;
using sys.db.pgsql.ByteTools;
using sys.db.pgsql.Messages;
import sys.db.pgsql.DataType;

class Postgres {

	public static function connect( params : {
		host     : String,
		?port    : Int,
		user     : String,
		pass     : String,
		?socket  : String,
		database : String
	} ) : Connection {
		return new PostgresConnection(params);
	}
}

class PostgresConnection implements sys.db.Connection {
	var status     : Map<String, String>;
	var process_id : Int;
	var secret_key : Int;
	var s     : sys.net.Socket;
	var database   : String;
	var bb : BytesBuffer;

	public function new( params : {
		host     : String,
		?port    : Int,
		user     : String,
		pass     : String,
		?socket  : String,
		database : String
	}){
		if (params.port == null) params.port = 5432;
		status = new Map();
		s = new Socket();
		s.input.bigEndian = true; 
		s.output.bigEndian = true;
		database = params.database;
		var h = new Host(params.host);
		s.connect(h, params.port);
		bb = new BytesBuffer();

		// write the startup message
		s.writeMessage(
				StartupMessage({
					user: params.user,
					database: params.database,
					// client_encoding: "'utf-8'" // typical
				})
		);

		// grab the next few optional status/data messages
		while(true){
			var msg = s.readMessage();
			switch(msg){
				case AuthenticationRequest(AuthenticationOk)  : null; //ok
				case ParameterStatus(args): status[args.name] = args.value;
				case BackendKeyData(args): {
					process_id = args.process_id;
					secret_key = args.secret_key;
				}
				case ReadyForQuery(status):  break;  // move on when ready
				default: throw ('Unimplemented: $msg');
			}
		}

		// connection is now in ready state
		return null;
	}

	public function request( query : String ): ResultSet {
		// write the query
		s.writeMessage( Query(query) );
		var msg = s.readMessage();
		var field_descriptions = new Array<FieldDescription>();
		// grab the first response, which is usually a row description 
		switch(msg){
			case ErrorResponse(args)  : throw('Error: $msg');
			case EmptyQueryResponse   : return null; 
			case RowDescription(args) : field_descriptions = args; 
			default: trace('Unimplemented: $msg');
		}
		var data = new Array<Array<Bytes>>();
		while(true){
			// the second response contains the actual data
			var msg = s.readMessage();
			switch(msg){
				case DataRow(args): data.push(args); 
				case CommandComplete(tag): break;
				default: trace('Unimplemented: $msg');
			}
		}
		var row_idx = 0;
		return new PostgresResultSet(data,field_descriptions);
	}
	public function close(): Void {
		s.close();
	}
	public function escape( s : String ): String {
		return null;
	}
	public function quote( s : String ): String {
		return null;
	}
	public function addValue( s : StringBuf, v : Dynamic ) : Void {
	}
	public function lastInsertId() : Int {
		return 0;
	}
	public function dbName() : String { 
		return this.database;
	}
	public function startTransaction() : Void {
	}
	public function commit() : Void {
	}
	public function rollback() : Void {
	}
}

class PostgresResultSet implements ResultSet {
	var data : Array<Array<Bytes>>;
	var field_descriptions: Array<FieldDescription>;
	var row_idx = 0;
	public var length(get, null): Int;
	public var nfields(get, null): Int;
	function get_length() return data.length;
	function get_nfields() return field_descriptions.length;
	public function new(data, field_descriptions){
		this.data = data;
		this.field_descriptions = field_descriptions;
	}
	public function getFieldsNames(){
		return [for (f in field_descriptions) f.name];
	}
	public function getFloatResult(col_idx: Int){
		var bytes = data[row_idx][col_idx];
		var bytes_input =  new BytesInput(bytes, 0, bytes.length);
		return bytes_input.readFloat();
	}
	public function	getIntResult(col_idx: Int){
		var bytes = data[row_idx][col_idx];
		var bytes_input =  new BytesInput(bytes, 0, bytes.length);
		return bytes_input.readInt32();
	}
	public function getResult(col_idx: Int){
		return data[row_idx][col_idx].toString();
	}
	public function hasNext() return (row_idx < data.length);
	public function next() {
		var obj = {};
		for (idx in 0...field_descriptions.length){
			var field_desc = field_descriptions[idx];
			var row = data[row_idx++];
			Reflect.setField( obj, 
					field_desc.name, 
					readType(field_desc.datatype_object_id, row[idx])
			);
		}
		return obj;
	}
	public function	results() return Lambda.list(data);
	static function readType(oid : DataType, bytes : Bytes): Dynamic {
		var output = new haxe.io.BytesInput(bytes, 0, bytes.length);
		return switch(oid){
			case DataType.oidINT8   : output.readInt32();
			case DataType.oidINT4   : output.readInt16();
			case DataType.oidINT2   : output.readInt8();
			case DataType.oidBOOL   : output.readInt8() > 0;
			case DataType.oidFLOAT4 : output.readFloat();
			case DataType.oidFLOAT8 : output.readDouble();
			default        : output.readString(bytes.length);
		}
		
	}
}
