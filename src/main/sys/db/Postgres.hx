package sys.db;

import haxe.io.Bytes;
import haxe.io.BytesBuffer;
import haxe.io.BytesInput;
import haxe.io.BytesOutput;
import haxe.io.Input;
import sys.db.pgsql.DataType;
import sys.net.Host;
import sys.net.Socket;

using haxe.crypto.Md5;
using sys.db.pgsql.ByteTools;
using sys.db.pgsql.Messages;

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
	var status         : Map<String, String>;
	var process_id     : Int;
	var secret_key     : Int;
	var socket         : Socket;
	var last_insert_id : Int;

	public function new( params : {
		database : String,
		host     : String,
		pass     : String,
		?port    : Int,
		?socket  : String,
		user     : String,
	}){
		if (params.port == null) params.port = 5432;
		status = new Map();
		socket = new Socket();
		socket.input.bigEndian = true;
		socket.output.bigEndian = true;
		var h = new Host(params.host);
		socket.connect(h, params.port);

		// write the startup message
		socket.writeMessage(
				StartupMessage({
					user     : params.user,
					database : params.database,
					// client_encoding: "'utf-8'" // typical
				})
		);

		// grab the next few optional status/data messages
		while (true) {
			switch socket.readMessage() {
				case AuthenticationRequest(request_type) : {
					switch(request_type){
						case AuthenticationOk : null; //ok
						case AuthenticationCleartextPassword :
							socket.writeMessage(PasswordMessage(params.pass));
						case AuthenticationMD5Password(salt) :
							socket.writeMessage(PasswordMessage(md5Auth({
								pass : params.pass,
								user : params.user,
								salt : salt
							})));
						case ni: throw ('Unimplemented: $ni');
					}
				}
				case ParameterStatus(args) : status[args.name] = args.value;
				case BackendKeyData(args)  : {
					process_id = args.process_id;
					secret_key = args.secret_key;
				}
				case ReadyForQuery(status) :  break;  // move on when ready
				case ni: throw ('Unimplemented: $ni');
			}
		}

		// connection is now in ready state
	}

	public function request( query : String ): ResultSet {
		// write the query
		socket.writeMessage( Query(query) );
		var msg = socket.readMessage();
		var field_descriptions = new Array<FieldDescription>();
		// grab the first response, which is usually a row description
		switch(msg){
			case ErrorResponse(args)  : throw('Error: $msg');
			case EmptyQueryResponse   : return null;
			case RowDescription(args) : field_descriptions = args;
			default : trace('Unimplemented: $msg');
		}
		var data = new Array<Array<Bytes>>();

		// TODO move this down to the result set next() function?
		while(true){
			// the second response contains the actual data
			var msg = socket.readMessage();
			switch(msg){
				case DataRow(args)         : data.push(args);
				case CommandComplete(tag)  : handleTag(tag);
				case ReadyForQuery(status) : break;
				default : trace('Unimplemented: $msg');
			}
		}
		var row_idx = 0;
		return new PostgresResultSet(data,field_descriptions);
	}

	public function close() socket.close();

	public function quote( s : String ): String {
		return s.split("'").join("''");
	}
	public function escape( s : String ): String {
		var s = s.split("\n").join("\\n");
		var s = s.split("\r").join("\\r");
		var s = s.split("\t").join("\\t");
		var s = s.split("\\").join("\\\\");
		var s = s.split("'").join("''");
		var s = s.split(String.fromCharCode(12)).join("\\f");
		return 'E\'$s\'';
	}
	public function addValue( s : StringBuf, v : Dynamic ) : Void {
		if (v == null || Std.is(v,Int)){
			s.add(v);
		} else if (Std.is(v,Bool)){
			s.add(if (cast v) 1 else 0);
		} else {
			s.add(quote(Std.string(v)));
		}
	}
	public function lastInsertId() return this.last_insert_id;
	public function dbName() return "PostgreSQL"; 
	public function startTransaction() request("BEGIN;");
	public function commit() request("COMMIT;"); 
	public function rollback() request("ROLLBACK;");

	/**
	  Utility function to create a postgres/md5 authentication string
	 **/
	inline static function md5Auth(o : {pass : String, user : String, salt : String}){
		return "md5" + ((o.pass + o.user).encode() + o.salt).encode();
	}

	/**
	  Utility function to handle postgres tags (and capture last insert id's)
	 **/
	inline public function handleTag(tag:String){
		var values = tag.split(' ');
		var command = values.pop();
		switch(command){
			case "INSERT" : {
				var oid  = Std.parseInt(values.pop());
				var rows = Std.parseInt(values.pop());
				if (rows == 1) this.last_insert_id = oid;
			}
		}
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
		var row = data[row_idx++];
		for (idx in 0...field_descriptions.length) {
			var field_desc = field_descriptions[idx];
			Reflect.setField( obj,
					field_desc.name,
					readType(field_desc.datatype_object_id, row[idx])
			);
		}
		return obj;
	}

	public function	results() return Lambda.list([ for (f in this) f] );

	/**
	  Utility function to parse a postgres timestamp into a Haxe date.
	 **/
	static function parseTimeStampTz(stamp:String){
		// split off time zone, can't parse those directly
		var tz = stamp.substring(stamp.length - 3, stamp.length);
		var offset = Std.parseInt(tz);

		// split off milliseconds, can't encode those
		var date = Date.fromString(stamp.split('.')[0]);

		// leap year adjustment
		var days = (date.getFullYear() % 4 == 0) ? 366 : 365;

		return date;

		//  modify the original stamp by the offset, giving utc
		// return DateTools.delta(date, offset / (24 * days));
	}

	/**
	  Utility function that will convert a bytes argument into a Haxe type
	  based on the postgres object id datatype.
	 **/
	static function readType(oid : DataType, bytes : Bytes): Dynamic {
		var output = new haxe.io.BytesInput(bytes, 0, bytes.length);
		return switch(oid){
			case DataType.oidINT8        : output.readInt32();
			case DataType.oidINT4        : output.readInt16();
			case DataType.oidINT2        : output.readInt8();
			case DataType.oidBOOL        : output.readInt8() > 0;
			case DataType.oidFLOAT4      : output.readFloat();
			case DataType.oidFLOAT8      : output.readDouble();
			case DataType.oidTIMESTAMPTZ : parseTimeStampTz(output.readString(bytes.length));
			default                      : output.readString(bytes.length);
		}

	}
}
