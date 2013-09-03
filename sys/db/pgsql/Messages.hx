package sys.db.pgsql;
import haxe.io.BytesBuffer;
import sys.net.Socket;
import haxe.io.Input;
import haxe.io.Output;
import haxe.io.Bytes;
typedef Int8 = Int;
typedef Int16 = Int;
typedef Int32 = Int;

class Messages {
	static inline var OFFSET = 4;
	public static function writeMessage(s: Socket, f: ClientMessageType): Void {
	    var w = new Writer();
		var buffer = switch(f){
			case StartupMessage(args): w.msgLength().addInt32(0x30000).addObj(args);
			case Query(query): w.addString("Q").msgLength().addCString(query);
		}
		var writer_bytes = w.getBytes();
		s.output.writeBytes(writer_bytes, 0, writer_bytes.length);
		return;
	}

	public static function readMessage(s: Socket) : ServerMessage {
		var input = s.input;
		var code = input.readString(1);
		var length = input.readInt32();
		length -= OFFSET;
		return switch(code){
			case "R" : {
				var code = input.readInt32();
				var type = switch(code){
					case 0 : AuthenticationOk;
					case 2 : AuthenticationKerberosV5;
					case 3 : AuthenticationCleartextPassword;
					case 5 : AuthenticationMD5Password(input.readString(4));
					// case 6 : AuthenticationSCMCredential;
					// case 7 : AuthenticationGSS;
					// case 9 : AuthenticationSSPI;
					// case 8 : AuthenticationGSSContinue(input.readString(length-4)); // int32 - byte
					case _ : AuthenticationUnknown;
				}
				AuthenticationRequest(type);
			}
			case "K" : BackendKeyData({
				process_id: input.readInt32(), 
				secret_key: input.readInt32()
			});
			// case "B" : BindComplete;
			// case "3" : CloseComplete;
			case "C" : CommandComplete(input.readString(length));
			// case "d" : CopyData(input.readString(length));
			// case "c" : CopyDone;
			// case "G" : CopyInResponse({
			// 	text_or_binary : input.readInt8(),
			// 	num_columns    : input.readInt16(),
			// 	formatCodes    : input.readInt16()
			// });
			// case "H" : CopyOutResponse({
			// 	text_or_binary : input.readInt8(),
			// 	num_columns    : input.readInt16(),
			// 	formatCodes    : input.readInt16()
			// });
			// case "W" : CopyBothResponse({
			// 	text_or_binary : input.readInt8(),
			// 	num_columns    : input.readInt16(),
			// 	formatCodes    : input.readInt16()
			// });
			case "D" : DataRow(
				[for (i in 0...input.readInt16()) input.read(input.readInt32())] 
			);
			case "I" : EmptyQueryResponse;
			case "E" : ErrorResponse({
				field_type  : input.readInt8(),
				field_value : input.readString(length)
			});
			// case "V" : FunctionCallResponse({
			// 	function_result_length : input.readInt32(),
			// 	function_result_value  : input.readString(length)
			// });
			// case "n" : NoData;
			// case "N" : NoticeResponse(decodeNoticeResponseFields(d));
			// case "A" : NotificationResponse;
			// case "t" : ParameterDescription;
			case "S" : ParameterStatus({
				var name = input.readUntil(0); // delimited by 0, adjust length below
				{ name  : name , value : input.readString(length - name.length - 1) }
			});

			// case "1" : ParseComplete;
			// case "s" : PortalSuspended;
			case "Z" : ReadyForQuery(input.readString(1));
			case "T" : RowDescription(
					[ for (i in 0...input.readInt16())
							{
								name               : input.readUntil(0),
								table_object_id    : input.readInt32(),
								table_attribute_id : input.readInt16(),
								datatype_object_id : input.readInt32(),
								datatype_size      : input.readInt16(),
								type_modifier      : input.readInt32(),
								format_code        : input.readInt16()
							}
					]
			);
			case  _  : Unknown;
		}
	}

	public static function decodeAuthenticationRequest
		(input : Input, length : Int) : ServerMessage {
		var code = input.readInt32();
		var type:AuthenticationRequestType = switch(code){
			case 0 : AuthenticationOk;
			case 2 : AuthenticationKerberosV5;
			case 3 : AuthenticationCleartextPassword;
			case 5 : AuthenticationMD5Password(input.readString(4));
			// case 6 : AuthenticationSCMCredential;
			// case 7 : AuthenticationGSS;
			// case 9 : AuthenticationSSPI;
			// case 8 : AuthenticationGSSContinue(input.readString(length-4)); // int32 - byte
			case _ : AuthenticationUnknown;
		}
		return AuthenticationRequest(type);
	}
}

enum AuthenticationRequestType{
	AuthenticationOk;
	AuthenticationKerberosV5;
	AuthenticationCleartextPassword;
	AuthenticationMD5Password(salt:String);
	AuthenticationSCMCredential;
	AuthenticationGSS;
	AuthenticationSSPI;
	AuthenticationGSSContinue(auth_data:String);
	AuthenticationUnknown;
}

typedef CopyResponseArguments = {
	text_or_binary:Int, 
	num_columns:Int, 
	formatCodes:Int
}

enum ServerMessage {
	AuthenticationRequest(authType:AuthenticationRequestType);
	BackendKeyData(args:{process_id:Int, secret_key:Int});
	BindComplete;
	CloseComplete;
	CommandComplete(tag:String);
	CopyData(stream:String);
	CopyDone;
	CopyInResponse(args: CopyResponseArguments);
	CopyOutResponse(args: CopyResponseArguments);
	CopyBothResponse(args: CopyResponseArguments);
	DataRow(fields : Array<Bytes>);
	EmptyQueryResponse;
	ErrorResponse(args:{field_type:Int, field_value:String});
	FunctionCallResponse(args:{function_result_length:Int, function_result_value:String});
	NoData;
	// NoticeResponse;
	// NotificationResponse;
	// ParameterDescription;
	ParameterStatus(args: {name: String, value: String});
	// ParseComplete;
	// PortalSuspended;
	ReadyForQuery(status:String);
	RowDescription( fields : Array<FieldDescription>);
	Unknown;
}
typedef FieldValue = {
	length : Int32,
	value : Bytes
}

typedef FieldDescription = {
	name               : String,
	table_object_id    : Int32,
	table_attribute_id : Int16,
	datatype_object_id : Int32,
	datatype_size      : Int16,
	type_modifier      : Int32,
	format_code        : Int16
}

typedef StartupArgs = {
	?user            : String,
	?pass            : String,
	?database        : String,
	?client_encoding : String,
}
typedef ConnectionArgs ={
	> StartupArgs,
	host : String,
	port : Int,
	socket :String,

}

enum ClientMessageType{
	StartupMessage(args: StartupArgs);
	// Bind;
	// CancelRequest;
	// Close;
	// CopyData;
	// CopyDone;
	// CopyFail;
	// Describe;
	// Execute;
	// Flush;
	// FunctionCall;
	// Parse;
	// PasswordMessage;
	Query(query: String);
	// SSLRequest;
	// Sync;
	// Terminate;
}
