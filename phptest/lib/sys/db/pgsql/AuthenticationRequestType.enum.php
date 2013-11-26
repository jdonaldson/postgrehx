<?php

class sys_db_pgsql_AuthenticationRequestType extends Enum {
	public static $AuthenticationCleartextPassword;
	public static $AuthenticationGSS;
	public static function AuthenticationGSSContinue($auth_data) { return new sys_db_pgsql_AuthenticationRequestType("AuthenticationGSSContinue", 7, array($auth_data)); }
	public static $AuthenticationKerberosV5;
	public static function AuthenticationMD5Password($salt) { return new sys_db_pgsql_AuthenticationRequestType("AuthenticationMD5Password", 3, array($salt)); }
	public static $AuthenticationOk;
	public static $AuthenticationSCMCredential;
	public static $AuthenticationSSPI;
	public static $AuthenticationUnknown;
	public static $__constructors = array(2 => 'AuthenticationCleartextPassword', 5 => 'AuthenticationGSS', 7 => 'AuthenticationGSSContinue', 1 => 'AuthenticationKerberosV5', 3 => 'AuthenticationMD5Password', 0 => 'AuthenticationOk', 4 => 'AuthenticationSCMCredential', 6 => 'AuthenticationSSPI', 8 => 'AuthenticationUnknown');
	}
sys_db_pgsql_AuthenticationRequestType::$AuthenticationCleartextPassword = new sys_db_pgsql_AuthenticationRequestType("AuthenticationCleartextPassword", 2);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationGSS = new sys_db_pgsql_AuthenticationRequestType("AuthenticationGSS", 5);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationKerberosV5 = new sys_db_pgsql_AuthenticationRequestType("AuthenticationKerberosV5", 1);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationOk = new sys_db_pgsql_AuthenticationRequestType("AuthenticationOk", 0);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationSCMCredential = new sys_db_pgsql_AuthenticationRequestType("AuthenticationSCMCredential", 4);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationSSPI = new sys_db_pgsql_AuthenticationRequestType("AuthenticationSSPI", 6);
sys_db_pgsql_AuthenticationRequestType::$AuthenticationUnknown = new sys_db_pgsql_AuthenticationRequestType("AuthenticationUnknown", 8);
