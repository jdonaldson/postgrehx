<?php

class sys_db_pgsql_ServerMessage extends Enum {
	public static function AuthenticationRequest($authType) { return new sys_db_pgsql_ServerMessage("AuthenticationRequest", 0, array($authType)); }
	public static function BackendKeyData($args) { return new sys_db_pgsql_ServerMessage("BackendKeyData", 1, array($args)); }
	public static $BindComplete;
	public static $CloseComplete;
	public static function CommandComplete($tag) { return new sys_db_pgsql_ServerMessage("CommandComplete", 4, array($tag)); }
	public static function CopyBothResponse($args) { return new sys_db_pgsql_ServerMessage("CopyBothResponse", 9, array($args)); }
	public static function CopyData($stream) { return new sys_db_pgsql_ServerMessage("CopyData", 5, array($stream)); }
	public static $CopyDone;
	public static function CopyInResponse($args) { return new sys_db_pgsql_ServerMessage("CopyInResponse", 7, array($args)); }
	public static function CopyOutResponse($args) { return new sys_db_pgsql_ServerMessage("CopyOutResponse", 8, array($args)); }
	public static function DataRow($fields) { return new sys_db_pgsql_ServerMessage("DataRow", 10, array($fields)); }
	public static $EmptyQueryResponse;
	public static function ErrorResponse($notice) { return new sys_db_pgsql_ServerMessage("ErrorResponse", 12, array($notice)); }
	public static function FunctionCallResponse($args) { return new sys_db_pgsql_ServerMessage("FunctionCallResponse", 13, array($args)); }
	public static $NoData;
	public static function NoticeResponse($notice) { return new sys_db_pgsql_ServerMessage("NoticeResponse", 15, array($notice)); }
	public static function NotificationResponse($args) { return new sys_db_pgsql_ServerMessage("NotificationResponse", 16, array($args)); }
	public static function ParameterStatus($args) { return new sys_db_pgsql_ServerMessage("ParameterStatus", 17, array($args)); }
	public static function ReadyForQuery($status) { return new sys_db_pgsql_ServerMessage("ReadyForQuery", 18, array($status)); }
	public static function RowDescription($fields) { return new sys_db_pgsql_ServerMessage("RowDescription", 19, array($fields)); }
	public static function Unknown($code) { return new sys_db_pgsql_ServerMessage("Unknown", 20, array($code)); }
	public static $__constructors = array(0 => 'AuthenticationRequest', 1 => 'BackendKeyData', 2 => 'BindComplete', 3 => 'CloseComplete', 4 => 'CommandComplete', 9 => 'CopyBothResponse', 5 => 'CopyData', 6 => 'CopyDone', 7 => 'CopyInResponse', 8 => 'CopyOutResponse', 10 => 'DataRow', 11 => 'EmptyQueryResponse', 12 => 'ErrorResponse', 13 => 'FunctionCallResponse', 14 => 'NoData', 15 => 'NoticeResponse', 16 => 'NotificationResponse', 17 => 'ParameterStatus', 18 => 'ReadyForQuery', 19 => 'RowDescription', 20 => 'Unknown');
	}
sys_db_pgsql_ServerMessage::$BindComplete = new sys_db_pgsql_ServerMessage("BindComplete", 2);
sys_db_pgsql_ServerMessage::$CloseComplete = new sys_db_pgsql_ServerMessage("CloseComplete", 3);
sys_db_pgsql_ServerMessage::$CopyDone = new sys_db_pgsql_ServerMessage("CopyDone", 6);
sys_db_pgsql_ServerMessage::$EmptyQueryResponse = new sys_db_pgsql_ServerMessage("EmptyQueryResponse", 11);
sys_db_pgsql_ServerMessage::$NoData = new sys_db_pgsql_ServerMessage("NoData", 14);
