package sys.db.pgsql;
@:fakeEnum
abstract DataType(Int) from Int to Int {
	inline public static var oidBOOL             = 16;
	inline public static var oidBYTEA            = 17;
	inline public static var oidCHAR             = 18;
	inline public static var oidNAME             = 19;
	inline public static var oidINT8             = 20;
	inline public static var oidINT2             = 21;
	inline public static var oidINT2VECTOR       = 22;
	inline public static var oidINT4             = 23;
	inline public static var oidREGPROC          = 24;
	inline public static var oidTEXT             = 25;
	inline public static var oidOID              = 26;
	inline public static var oidTID              = 27;
	inline public static var oidXID              = 28;
	inline public static var oidCID              = 29;
	inline public static var oidVECTOROID        = 30;
	inline public static var oidJSON             = 114;
	inline public static var oidXML              = 142;
	inline public static var oidPGNODETREE       = 194;
	inline public static var oidPOINT            = 600;
	inline public static var oidLSEG             = 601;
	inline public static var oidPATH             = 602;
	inline public static var oidBOX              = 603;
	inline public static var oidPOLYGON          = 604;
	inline public static var oidLINE             = 628;
	inline public static var oidFLOAT4           = 700;
	inline public static var oidFLOAT8           = 701;
	inline public static var oidABSTIME          = 702;
	inline public static var oidRELTIME          = 703;
	inline public static var oidTINTERVAL        = 704;
	inline public static var oidUNKNOWN          = 705;
	inline public static var oidCIRCLE           = 718;
	inline public static var oidCASH             = 790;
	inline public static var oidMACADDR          = 829;
	inline public static var oidINET             = 869;
	inline public static var oidCIDR             = 650;
	inline public static var oidINT4ARRAY        = 1007;
	inline public static var oidTEXTARRAY        = 1009;
	inline public static var oidFLOAT4ARRAY      = 1021;
	inline public static var oidACLITEM          = 1033;
	inline public static var oidCSTRINGARRAY     = 1263;
	inline public static var oidBPCHAR           = 1042;
	inline public static var oidVARCHAR          = 1043;
	inline public static var oidDATE             = 1082;
	inline public static var oidTIME             = 1083;
	inline public static var oidTIMESTAMP        = 1114;
	inline public static var oidTIMESTAMPTZ      = 1184;
	inline public static var oidINTERVAL         = 1186;
	inline public static var oidTIMETZ           = 1266;
	inline public static var oidBIT              = 1560;
	inline public static var oidVARBIT           = 1562;
	inline public static var oidNUMERIC          = 1700;
	inline public static var oidREFCURSOR        = 1790;
	inline public static var oidREGPROCEDURE     = 2202;
	inline public static var oidREGOPER          = 2203;
	inline public static var oidREGOPERATOR      = 2204;
	inline public static var oidREGCLASS         = 2205;
	inline public static var oidREGTYPE          = 2206;
	inline public static var oidREGTYPEARRAY     = 2211;
	inline public static var oidUUID             = 2950;
	inline public static var oidTSVECTOR         = 3614;
	inline public static var oidGTSVECTOR        = 3642;
	inline public static var oidTSQUERY          = 3615;
	inline public static var oidREGCONFIG        = 3734;
	inline public static var oidREGDICTIONARY    = 3769;
	inline public static var oidINT4RANGE        = 3904;
	inline public static var oidRECORD           = 2249;
	inline public static var oidRECORDARRAY      = 2287;
	inline public static var oidCSTRING          = 2275;
	inline public static var oidANY              = 2276;
	inline public static var oidANYARRAY         = 2277;
	inline public static var oidVOID             = 2278;
	inline public static var oidTRIGGER          = 2279;
	inline public static var oidEVTTRIGGER       = 3838;
	inline public static var oidLANGUAGE_HANDLER = 2280;
	inline public static var oidINTERNAL         = 2281;
	inline public static var oidOPAQUE           = 2282;
	inline public static var oidANYELEMENT       = 2283;
	inline public static var oidANYNONARRAY      = 2776;
	inline public static var oidANYENUM          = 3500;
	inline public static var oidFDW_HANDLER      = 3115;
	inline public static var oidANYRANGE         = 3831;
}
