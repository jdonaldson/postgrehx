<?php

class sys_db_pgsql_Messages {
	public function __construct(){}
	static function writeMessage($s, $f) {
		$w = new sys_db_pgsql_Writer();
		$buffer = sys_db_pgsql_Messages_0($f, $s, $w);
		$writer_bytes = $w->getBytes();
		$s->output->writeBytes($writer_bytes, 0, $writer_bytes->length);
		return;
	}
	static function readMessage($s) {
		$input = $s->input;
		$code = $input->readString(1);
		$length = $input->readInt32() - 4;
		return sys_db_pgsql_Messages_1($code, $input, $length, $s);
	}
	static function decodeNotice($input) {
		$byte_code = null;
		$notice = _hx_anonymous(array());
		while(sys_db_pgsql_Messages_2($byte_code, $input, $notice)) {
			$notice->{sys_db_pgsql_Messages_3($byte_code, $input, $notice)} = $input->readUntil(0);
		}
		return new sys_db_pgsql_Notice($notice);
	}
	static function noticeFieldToString($code) {
		return sys_db_pgsql_Messages_4($code);
	}
	static function decodeAuthenticationRequest($input, $length) {
		$code = $input->readInt32();
		$type = sys_db_pgsql_Messages_5($code, $input, $length);
		return sys_db_pgsql_ServerMessage::AuthenticationRequest($type);
	}
	function __toString() { return 'sys.db.pgsql.Messages'; }
}
function sys_db_pgsql_Messages_0(&$f, &$s, &$w) {
	$__hx__t = ($f);
	switch($__hx__t->index) {
	case 0:
	$args = $__hx__t->params[0];
	{
		return $w->msgLength()->addInt32(196608)->addObj($args);
	}break;
	case 1:
	$s1 = $__hx__t->params[0];
	{
		return $w->addString("p")->msgLength()->addCString($s1);
	}break;
	case 2:
	$query = $__hx__t->params[0];
	{
		return $w->addString("Q")->msgLength()->addCString($query);
	}break;
	}
}
function sys_db_pgsql_Messages_1(&$code, &$input, &$length, &$s) {
	switch($code) {
	case "R":{
		$code1 = $input->readInt32();
		$type = sys_db_pgsql_Messages_6($code, $code1, $input, $length, $s);
		return sys_db_pgsql_ServerMessage::AuthenticationRequest($type);
	}break;
	case "K":{
		return sys_db_pgsql_ServerMessage::BackendKeyData(_hx_anonymous(array("process_id" => $input->readInt32(), "secret_key" => $input->readInt32())));
	}break;
	case "C":{
		return sys_db_pgsql_ServerMessage::CommandComplete($input->readString($length));
	}break;
	case "D":{
		return sys_db_pgsql_ServerMessage::DataRow(sys_db_pgsql_Messages_7($code, $input, $length, $s));
	}break;
	case "I":{
		return sys_db_pgsql_ServerMessage::$EmptyQueryResponse;
	}break;
	case "E":{
		return sys_db_pgsql_ServerMessage::ErrorResponse(sys_db_pgsql_Messages::decodeNotice($input));
	}break;
	case "N":{
		return sys_db_pgsql_ServerMessage::NoticeResponse(sys_db_pgsql_Messages::decodeNotice($input));
	}break;
	case "A":{
		return sys_db_pgsql_ServerMessage::NotificationResponse(_hx_anonymous(array("process_id" => $input->readInt32(), "channel" => $input->readUntil(0), "payload" => $input->readUntil(0))));
	}break;
	case "S":{
		return sys_db_pgsql_ServerMessage::ParameterStatus(sys_db_pgsql_Messages_8($code, $input, $length, $s));
	}break;
	case "Z":{
		return sys_db_pgsql_ServerMessage::ReadyForQuery($input->readString(1));
	}break;
	case "T":{
		return sys_db_pgsql_ServerMessage::RowDescription(sys_db_pgsql_Messages_9($code, $input, $length, $s));
	}break;
	default:{
		return sys_db_pgsql_ServerMessage::Unknown($code);
	}break;
	}
}
function sys_db_pgsql_Messages_2(&$byte_code, &$input, &$notice) {
	{
		$byte_code = $input->readByte();
		return $byte_code !== 0;
	}
}
function sys_db_pgsql_Messages_3(&$byte_code, &$input, &$notice) {
	{
		$code = chr($byte_code);
		switch($code) {
		case "S":{
			return "severity";
		}break;
		case "C":{
			return "sqlstate";
		}break;
		case "M":{
			return "message";
		}break;
		case "D":{
			return "detail";
		}break;
		case "H":{
			return "hint";
		}break;
		case "P":{
			return "position";
		}break;
		case "q":{
			return "query";
		}break;
		case "W":{
			return "where";
		}break;
		case "F":{
			return "file";
		}break;
		case "L":{
			return "line";
		}break;
		case "R":{
			return "routine";
		}break;
		default:{
			return "unknown_" . _hx_string_or_null($code);
		}break;
		}
		unset($code);
	}
}
function sys_db_pgsql_Messages_4(&$code) {
	switch($code) {
	case "S":{
		return "severity";
	}break;
	case "C":{
		return "sqlstate";
	}break;
	case "M":{
		return "message";
	}break;
	case "D":{
		return "detail";
	}break;
	case "H":{
		return "hint";
	}break;
	case "P":{
		return "position";
	}break;
	case "q":{
		return "query";
	}break;
	case "W":{
		return "where";
	}break;
	case "F":{
		return "file";
	}break;
	case "L":{
		return "line";
	}break;
	case "R":{
		return "routine";
	}break;
	default:{
		return "unknown_" . _hx_string_or_null($code);
	}break;
	}
}
function sys_db_pgsql_Messages_5(&$code, &$input, &$length) {
	switch($code) {
	case 0:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationOk;
	}break;
	case 2:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationKerberosV5;
	}break;
	case 3:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationCleartextPassword;
	}break;
	case 5:{
		return sys_db_pgsql_AuthenticationRequestType::AuthenticationMD5Password($input->readString(4));
	}break;
	default:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationUnknown;
	}break;
	}
}
function sys_db_pgsql_Messages_6(&$code, &$code1, &$input, &$length, &$s) {
	switch($code1) {
	case 0:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationOk;
	}break;
	case 2:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationKerberosV5;
	}break;
	case 3:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationCleartextPassword;
	}break;
	case 5:{
		return sys_db_pgsql_AuthenticationRequestType::AuthenticationMD5Password($input->readString(4));
	}break;
	default:{
		return sys_db_pgsql_AuthenticationRequestType::$AuthenticationUnknown;
	}break;
	}
}
function sys_db_pgsql_Messages_7(&$code, &$input, &$length, &$s) {
	{
		$_g = new _hx_array(array());
		{
			$_g2 = 0; $_g1 = $input->readInt16();
			while($_g2 < $_g1) {
				$i = $_g2++;
				$_g->push(sys_db_pgsql_Messages_10($_g, $_g1, $_g2, $code, $i, $input, $length, $s));
				unset($i);
			}
		}
		return $_g;
	}
}
function sys_db_pgsql_Messages_8(&$code, &$input, &$length, &$s) {
	{
		$name = $input->readUntil(0);
		return _hx_anonymous(array("name" => $name, "value" => $input->readString($length - strlen($name) - 1)));
	}
}
function sys_db_pgsql_Messages_9(&$code, &$input, &$length, &$s) {
	{
		$_g = new _hx_array(array());
		{
			$_g2 = 0; $_g1 = $input->readInt16();
			while($_g2 < $_g1) {
				$i = $_g2++;
				$_g->push(_hx_anonymous(array("name" => $input->readUntil(0), "table_object_id" => $input->readInt32(), "table_attribute_id" => $input->readInt16(), "datatype_object_id" => $input->readInt32(), "datatype_size" => $input->readInt16(), "type_modifier" => $input->readInt32(), "format_code" => $input->readInt16())));
				unset($i);
			}
		}
		return $_g;
	}
}
function sys_db_pgsql_Messages_10(&$_g, &$_g1, &$_g2, &$code, &$i, &$input, &$length, &$s) {
	{
		$length1 = $input->readInt32();
		if($length1 === -1) {
			return null;
		} else {
			return $input->read($length1);
		}
		unset($length1);
	}
}
