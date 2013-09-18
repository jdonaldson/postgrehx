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
		writeMessage(
				StartupMessage({
					user     : params.user,
					database : params.database,
					// client_encoding: "'utf-8'" // default
				})
		);

		// grab the next few optional status/data messages
		while (true) {
			switch readMessage() {
				case AuthenticationRequest(request_type) : {
					switch(request_type){
						case AuthenticationOk : null; //ok
						case AuthenticationCleartextPassword :
							writeMessage(PasswordMessage(params.pass));
						case AuthenticationMD5Password(salt) :
							writeMessage(PasswordMessage(md5Auth({
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

    public function getDataRows(row_description: Array<FieldDescription>)
        : CommandCompletion {
        var results: Array<Array<Bytes>> = [];
        var set_tag: String = null;
        while(true){
            switch(readMessage()){
                case DataRow(args)        : results.push(args);
                case CommandComplete(tag) : { set_tag = tag; break;}
                case ni                   : unexpectedMessage(ni);
            }
        }
        return DataRows(row_description, results, set_tag);
    }

    inline static function unexpectedMessage(msg : ServerMessage){
        throw 'Unexpected message $msg in this state';

    }

    public function getCompletions() : Array<CommandCompletion> {
        var results: Array<CommandCompletion> = [];
        while(true){
            switch(readMessage()){
                case EmptyQueryResponse    : results.push(EmptyQueryResponse);
                case CommandComplete(tag)  : results.push(CommandComplete(tag));
                case RowDescription(args)  : results.push(getDataRows(args));
                case ReadyForQuery(status) : break;
                case ni                    : unexpectedMessage(ni);
            }
        }
        return results;
    }

	public function request( query : String ): ResultSet {
		// write the query
		writeMessage( Query(query) );
        var completions = getCompletions();
        var first_completion = completions[0];
        switch(first_completion){
            case EmptyQueryResponse : return new PostgresResultSet([],[]);
            case CommandComplete(tag) : {
                handleTag(tag);
                return new PostgresResultSet([], []);
            }
            case DataRows(row_description, data_rows, tag) : {
                return new PostgresResultSet(row_description, data_rows);
            }
        }
	}

	public function handleError(notice){
		while(true){
			switch(socket.readMessage()){
				case ReadyForQuery(status) : break;
				case ni                    : unexpectedMessage(ni);
			}
		}
		throw(notice);
	}

	public function close() socket.close();

    /**
      Use postgres escape quote: E'escaped string'
     **/
	public function quote( s : String ): String {
		var repl_quote = s.split("'").join("''");
		return 'E\'$repl_quote\'';
	}

	/**
	  Escape a string for a Postgres query.  Note that quote is
	  a safer option.
	 **/
	public function escape( s : String ): String {
		var s = s.split("\n").join("\\n");
		var s = s.split("\r").join("\\r");
		var s = s.split("\t").join("\\t");
		var s = s.split("\\").join("\\\\");
		var s = s.split("'").join("''");
		var s = s.split(String.fromCharCode(12)).join("\\f");
		return s;
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
	  Utility function that wraps the socket writeMessage function.  Mainly
	  provided for symmetry.
	 **/
	function writeMessage( msg : ClientMessageType){
		socket.writeMessage(msg);
	}

	/**
	  Utility function that wraps the normal socket.readMessage function.
	  This one will filter out and save ParameterStatus messages, which
	  can occur at any time during a Postgres session. It also will throw
	  errors from ErrorResponse messages, and filter out NotificationResponse
	  messages.
	 */
	function readMessage() : ServerMessage {
		while (true){
			switch(socket.readMessage()){
				case ParameterStatus(args) : status[args.name] = args.value;
				case ErrorResponse(notice) : handleError(notice);
				case NoticeResponse(notice) : null; // TODO : do something with this?
				case ni : return ni;
			}
		}
	}

	/**
	  Utility function to handle postgres tags (and capture last insert id's)
	 **/
	function handleTag(tag:String){
		var values = tag.split(' ');
		var command = values.pop();
		switch(command){
			case "INSERT" : {
				var oid  = Std.parseInt(values.pop());
				var rows = Std.parseInt(values.pop());
				if (rows == 1) this.last_insert_id = oid;
			}
			case "CREATE TABLE" : {
			    null;
			}


		}
	}
}

class PostgresResultSet implements ResultSet {
	var data : Array<Array<Bytes>>;
	var field_descriptions: Array<FieldDescription>;
	var row_idx = 0;
	public var length(get, null)  : Int;
	public var nfields(get, null) : Int;
	function get_length() return data.length;
	function get_nfields() return field_descriptions.length;
	public function new(field_descriptions, data){
		this.data = data;
		this.field_descriptions = field_descriptions;
	}
	public function getFieldsNames(){
		return [for (f in field_descriptions) f.name];
	}
	public function getFloatResult(col_idx: Int){
		var bytes       = data[row_idx][col_idx];
		var bytes_input = new BytesInput(bytes, 0, bytes.length);
		return bytes_input.readFloat();
	}

	public function	getIntResult(col_idx: Int){
		var bytes       = data[row_idx][col_idx];
		var bytes_input = new BytesInput(bytes, 0, bytes.length);
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
		// var days = (date.getFullYear() % 4 == 0) ? 366 : 365;

		return date;

		//  modify the original stamp by the offset, giving utc
		// return DateTools.delta(date, offset / (24 * days));
	}

	/**
	  Utility function that will convert a bytes argument into a Haxe type
	  based on the postgres object id datatype.
	 **/
	static function readType(oid : DataType, bytes : Bytes): Dynamic {
		var string = bytes.toString();
		return switch(oid){
			case DataType.oidINT8        : Std.parseInt(string);
			case DataType.oidINT4        : Std.parseInt(string);
			case DataType.oidINT2        : Std.parseInt(string);
			case DataType.oidBOOL        : Std.parseInt(string);
			case DataType.oidFLOAT4      : Std.parseFloat(string);
			case DataType.oidFLOAT8      : Std.parseFloat(string);
			case DataType.oidTIMESTAMPTZ : parseTimeStampTz(string);
			default                      : string;
		}

	}
}

/**
  This enum contains all of the relevant responses for a given command.
  This can result in an empty query response, a command complete tag,
  or a combination of row descriptions, row data, and a completion tag.
 **/
enum CommandCompletion {
    EmptyQueryResponse;
    CommandComplete(tag:String);
    DataRows(row_description: Array<FieldDescription>
            , data_rows: Array<Array<Bytes>>
            , tag : String
            );
}

