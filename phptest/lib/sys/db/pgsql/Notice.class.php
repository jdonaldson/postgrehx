<?php

class sys_db_pgsql_Notice {
	public function __construct($notice) {
		if(!php_Boot::$skip_constructor) {
		$this->severity = $notice->severity;
		$this->sqlstate = $notice->sqlstate;
		$this->message = $notice->message;
		$this->detail = $notice->detail;
		$this->hint = $notice->hint;
		$this->position = $notice->position;
		$this->query = $notice->query;
		$this->where = $notice->where;
		$this->file = $notice->file;
		$this->line = $notice->line;
		$this->routine = $notice->routine;
	}}
	public $routine;
	public $line;
	public $file;
	public $where;
	public $query;
	public $position;
	public $hint;
	public $detail;
	public $message;
	public $sqlstate;
	public $severity;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static function translateSqlState($code) {
		return sys_db_pgsql_Notice_0($code);
	}
	function __toString() { return 'sys.db.pgsql.Notice'; }
}
function sys_db_pgsql_Notice_0(&$code) {
	switch($code) {
	case "00000":{
		return "successful_completion";
	}break;
	case "01000":{
		return "warning";
	}break;
	case "0100C":{
		return "dynamic_result_sets_returned";
	}break;
	case "01008":{
		return "implicit_zero_bit_padding";
	}break;
	case "01003":{
		return "null_value_eliminated_in_set_function";
	}break;
	case "01007":{
		return "privilege_not_granted";
	}break;
	case "01006":{
		return "privilege_not_revoked";
	}break;
	case "01004":{
		return "string_data_right_truncation";
	}break;
	case "01P01":{
		return "deprecated_feature";
	}break;
	case "02000":{
		return "no_data";
	}break;
	case "02001":{
		return "no_additional_dynamic_result_sets_returned";
	}break;
	case "03000":{
		return "sql_statement_not_yet_complete";
	}break;
	case "08000":{
		return "connection_exception";
	}break;
	case "08003":{
		return "connection_does_not_exist";
	}break;
	case "08006":{
		return "connection_failure";
	}break;
	case "08001":{
		return "sqlclient_unable_to_establish_sqlconnection";
	}break;
	case "08004":{
		return "sqlserver_rejected_establishment_of_sqlconnection";
	}break;
	case "08007":{
		return "transaction_resolution_unknown";
	}break;
	case "08P01":{
		return "protocol_violation";
	}break;
	case "09000":{
		return "triggered_action_exception";
	}break;
	case "0A000":{
		return "feature_not_supported";
	}break;
	case "0B000":{
		return "invalid_transaction_initiation";
	}break;
	case "0F000":{
		return "locator_exception";
	}break;
	case "0F001":{
		return "invalid_locator_specification";
	}break;
	case "0L000":{
		return "invalid_grantor";
	}break;
	case "0LP01":{
		return "invalid_grant_operation";
	}break;
	case "0P000":{
		return "invalid_role_specification";
	}break;
	case "0Z000":{
		return "diagnostics_exception";
	}break;
	case "0Z002":{
		return "stacked_diagnostics_accessed_without_active_handler";
	}break;
	case "20000":{
		return "case_not_found";
	}break;
	case "21000":{
		return "cardinality_violation";
	}break;
	case "22000":{
		return "data_exception";
	}break;
	case "2202E":{
		return "array_subscript_error";
	}break;
	case "22021":{
		return "character_not_in_repertoire";
	}break;
	case "22008":{
		return "datetime_field_overflow";
	}break;
	case "22012":{
		return "division_by_zero";
	}break;
	case "22005":{
		return "error_in_assignment";
	}break;
	case "2200B":{
		return "escape_character_conflict";
	}break;
	case "22022":{
		return "indicator_overflow";
	}break;
	case "22015":{
		return "interval_field_overflow";
	}break;
	case "2201E":{
		return "invalid_argument_for_logarithm";
	}break;
	case "22014":{
		return "invalid_argument_for_ntile_function";
	}break;
	case "22016":{
		return "invalid_argument_for_nth_value_function";
	}break;
	case "2201F":{
		return "invalid_argument_for_power_function";
	}break;
	case "2201G":{
		return "invalid_argument_for_width_bucket_function";
	}break;
	case "22018":{
		return "invalid_character_value_for_cast";
	}break;
	case "22007":{
		return "invalid_datetime_format";
	}break;
	case "22019":{
		return "invalid_escape_character";
	}break;
	case "2200D":{
		return "invalid_escape_octet";
	}break;
	case "22025":{
		return "invalid_escape_sequence";
	}break;
	case "22P06":{
		return "nonstandard_use_of_escape_character";
	}break;
	case "22010":{
		return "invalid_indicator_parameter_value";
	}break;
	case "22023":{
		return "invalid_parameter_value";
	}break;
	case "2201B":{
		return "invalid_regular_expression";
	}break;
	case "2201W":{
		return "invalid_row_count_in_limit_clause";
	}break;
	case "2201X":{
		return "invalid_row_count_in_result_offset_clause";
	}break;
	case "22009":{
		return "invalid_time_zone_displacement_value";
	}break;
	case "2200C":{
		return "invalid_use_of_escape_character";
	}break;
	case "2200G":{
		return "most_specific_type_mismatch";
	}break;
	case "22004":{
		return "null_value_not_allowed";
	}break;
	case "22002":{
		return "null_value_no_indicator_parameter";
	}break;
	case "22003":{
		return "numeric_value_out_of_range";
	}break;
	case "22026":{
		return "string_data_length_mismatch";
	}break;
	case "22001":{
		return "string_data_right_truncation";
	}break;
	case "22011":{
		return "substring_error";
	}break;
	case "22027":{
		return "trim_error";
	}break;
	case "22024":{
		return "unterminated_c_string";
	}break;
	case "2200F":{
		return "zero_length_character_string";
	}break;
	case "22P01":{
		return "floating_point_exception";
	}break;
	case "22P02":{
		return "invalid_text_representation";
	}break;
	case "22P03":{
		return "invalid_binary_representation";
	}break;
	case "22P04":{
		return "bad_copy_file_format";
	}break;
	case "22P05":{
		return "untranslatable_character";
	}break;
	case "2200L":{
		return "not_an_xml_document";
	}break;
	case "2200M":{
		return "invalid_xml_document";
	}break;
	case "2200N":{
		return "invalid_xml_content";
	}break;
	case "2200S":{
		return "invalid_xml_comment";
	}break;
	case "2200T":{
		return "invalid_xml_processing_instruction";
	}break;
	case "23000":{
		return "integrity_constraint_violation";
	}break;
	case "23001":{
		return "restrict_violation";
	}break;
	case "23502":{
		return "not_null_violation";
	}break;
	case "23503":{
		return "foreign_key_violation";
	}break;
	case "23505":{
		return "unique_violation";
	}break;
	case "23514":{
		return "check_violation";
	}break;
	case "23P01":{
		return "exclusion_violation";
	}break;
	case "24000":{
		return "invalid_cursor_state";
	}break;
	case "25000":{
		return "invalid_transaction_state";
	}break;
	case "25001":{
		return "active_sql_transaction";
	}break;
	case "25002":{
		return "branch_transaction_already_active";
	}break;
	case "25008":{
		return "held_cursor_requires_same_isolation_level";
	}break;
	case "25003":{
		return "inappropriate_access_mode_for_branch_transaction";
	}break;
	case "25004":{
		return "inappropriate_isolation_level_for_branch_transaction";
	}break;
	case "25005":{
		return "no_active_sql_transaction_for_branch_transaction";
	}break;
	case "25006":{
		return "read_only_sql_transaction";
	}break;
	case "25007":{
		return "schema_and_data_statement_mixing_not_supported";
	}break;
	case "25P01":{
		return "no_active_sql_transaction";
	}break;
	case "25P02":{
		return "in_failed_sql_transaction";
	}break;
	case "26000":{
		return "invalid_sql_statement_name";
	}break;
	case "27000":{
		return "triggered_data_change_violation";
	}break;
	case "28000":{
		return "invalid_authorization_specification";
	}break;
	case "28P01":{
		return "invalid_password";
	}break;
	case "2B000":{
		return "dependent_privilege_descriptors_still_exist";
	}break;
	case "2BP01":{
		return "dependent_objects_still_exist";
	}break;
	case "2D000":{
		return "invalid_transaction_termination";
	}break;
	case "2F000":{
		return "sql_routine_exception";
	}break;
	case "2F005":{
		return "function_executed_no_return_statement";
	}break;
	case "2F002":{
		return "modifying_sql_data_not_permitted";
	}break;
	case "2F003":{
		return "prohibited_sql_statement_attempted";
	}break;
	case "2F004":{
		return "reading_sql_data_not_permitted";
	}break;
	case "34000":{
		return "invalid_cursor_name";
	}break;
	case "38000":{
		return "external_routine_exception";
	}break;
	case "38001":{
		return "containing_sql_not_permitted";
	}break;
	case "38002":{
		return "modifying_sql_data_not_permitted";
	}break;
	case "38003":{
		return "prohibited_sql_statement_attempted";
	}break;
	case "38004":{
		return "reading_sql_data_not_permitted";
	}break;
	case "39000":{
		return "external_routine_invocation_exception";
	}break;
	case "39001":{
		return "invalid_sqlstate_returned";
	}break;
	case "39004":{
		return "null_value_not_allowed";
	}break;
	case "39P01":{
		return "trigger_protocol_violated";
	}break;
	case "39P02":{
		return "srf_protocol_violated";
	}break;
	case "3B000":{
		return "savepoint_exception";
	}break;
	case "3B001":{
		return "invalid_savepoint_specification";
	}break;
	case "3D000":{
		return "invalid_catalog_name";
	}break;
	case "3F000":{
		return "invalid_schema_name";
	}break;
	case "40000":{
		return "transaction_rollback";
	}break;
	case "40002":{
		return "transaction_integrity_constraint_violation";
	}break;
	case "40001":{
		return "serialization_failure";
	}break;
	case "40003":{
		return "statement_completion_unknown";
	}break;
	case "40P01":{
		return "deadlock_detected";
	}break;
	case "42000":{
		return "syntax_error_or_access_rule_violation";
	}break;
	case "42601":{
		return "syntax_error";
	}break;
	case "42501":{
		return "insufficient_privilege";
	}break;
	case "42846":{
		return "cannot_coerce";
	}break;
	case "42803":{
		return "grouping_error";
	}break;
	case "42P20":{
		return "windowing_error";
	}break;
	case "42P19":{
		return "invalid_recursion";
	}break;
	case "42830":{
		return "invalid_foreign_key";
	}break;
	case "42602":{
		return "invalid_name";
	}break;
	case "42622":{
		return "name_too_long";
	}break;
	case "42939":{
		return "reserved_name";
	}break;
	case "42804":{
		return "datatype_mismatch";
	}break;
	case "42P18":{
		return "indeterminate_datatype";
	}break;
	case "42P21":{
		return "collation_mismatch";
	}break;
	case "42P22":{
		return "indeterminate_collation";
	}break;
	case "42809":{
		return "wrong_object_type";
	}break;
	case "42703":{
		return "undefined_column";
	}break;
	case "42883":{
		return "undefined_function";
	}break;
	case "42P01":{
		return "undefined_table";
	}break;
	case "42P02":{
		return "undefined_parameter";
	}break;
	case "42704":{
		return "undefined_object";
	}break;
	case "42701":{
		return "duplicate_column";
	}break;
	case "42P03":{
		return "duplicate_cursor";
	}break;
	case "42P04":{
		return "duplicate_database";
	}break;
	case "42723":{
		return "duplicate_function";
	}break;
	case "42P05":{
		return "duplicate_prepared_statement";
	}break;
	case "42P06":{
		return "duplicate_schema";
	}break;
	case "42P07":{
		return "duplicate_table";
	}break;
	case "42712":{
		return "duplicate_alias";
	}break;
	case "42710":{
		return "duplicate_object";
	}break;
	case "42702":{
		return "ambiguous_column";
	}break;
	case "42725":{
		return "ambiguous_function";
	}break;
	case "42P08":{
		return "ambiguous_parameter";
	}break;
	case "42P09":{
		return "ambiguous_alias";
	}break;
	case "42P10":{
		return "invalid_column_reference";
	}break;
	case "42611":{
		return "invalid_column_definition";
	}break;
	case "42P11":{
		return "invalid_cursor_definition";
	}break;
	case "42P12":{
		return "invalid_database_definition";
	}break;
	case "42P13":{
		return "invalid_function_definition";
	}break;
	case "42P14":{
		return "invalid_prepared_statement_definition";
	}break;
	case "42P15":{
		return "invalid_schema_definition";
	}break;
	case "42P16":{
		return "invalid_table_definition";
	}break;
	case "42P17":{
		return "invalid_object_definition";
	}break;
	case "44000":{
		return "with_check_option_violation";
	}break;
	case "53000":{
		return "insufficient_resources";
	}break;
	case "53100":{
		return "disk_full";
	}break;
	case "53200":{
		return "out_of_memory";
	}break;
	case "53300":{
		return "too_many_connections";
	}break;
	case "53400":{
		return "configuration_limit_exceeded";
	}break;
	case "54000":{
		return "program_limit_exceeded";
	}break;
	case "54001":{
		return "statement_too_complex";
	}break;
	case "54011":{
		return "too_many_columns";
	}break;
	case "54023":{
		return "too_many_arguments";
	}break;
	case "55000":{
		return "object_not_in_prerequisite_state";
	}break;
	case "55006":{
		return "object_in_use";
	}break;
	case "55P02":{
		return "cant_change_runtime_param";
	}break;
	case "55P03":{
		return "lock_not_available";
	}break;
	case "57000":{
		return "operator_intervention";
	}break;
	case "57014":{
		return "query_canceled";
	}break;
	case "57P01":{
		return "admin_shutdown";
	}break;
	case "57P02":{
		return "crash_shutdown";
	}break;
	case "57P03":{
		return "cannot_connect_now";
	}break;
	case "57P04":{
		return "database_dropped";
	}break;
	case "58000":{
		return "system_error";
	}break;
	case "58030":{
		return "io_error";
	}break;
	case "58P01":{
		return "undefined_file";
	}break;
	case "58P02":{
		return "duplicate_file";
	}break;
	case "F0000":{
		return "config_file_error";
	}break;
	case "F0001":{
		return "lock_file_exists";
	}break;
	case "HV000":{
		return "fdw_error";
	}break;
	case "HV005":{
		return "fdw_column_name_not_found";
	}break;
	case "HV002":{
		return "fdw_dynamic_parameter_value_needed";
	}break;
	case "HV010":{
		return "fdw_function_sequence_error";
	}break;
	case "HV021":{
		return "fdw_inconsistent_descriptor_information";
	}break;
	case "HV024":{
		return "fdw_invalid_attribute_value";
	}break;
	case "HV007":{
		return "fdw_invalid_column_name";
	}break;
	case "HV008":{
		return "fdw_invalid_column_number";
	}break;
	case "HV004":{
		return "fdw_invalid_data_type";
	}break;
	case "HV006":{
		return "fdw_invalid_data_type_descriptors";
	}break;
	case "HV091":{
		return "fdw_invalid_descriptor_field_identifier";
	}break;
	case "HV00B":{
		return "fdw_invalid_handle";
	}break;
	case "HV00C":{
		return "fdw_invalid_option_index";
	}break;
	case "HV00D":{
		return "fdw_invalid_option_name";
	}break;
	case "HV090":{
		return "fdw_invalid_string_length_or_buffer_length";
	}break;
	case "HV00A":{
		return "fdw_invalid_string_format";
	}break;
	case "HV009":{
		return "fdw_invalid_use_of_null_pointer";
	}break;
	case "HV014":{
		return "fdw_too_many_handles";
	}break;
	case "HV001":{
		return "fdw_out_of_memory";
	}break;
	case "HV00P":{
		return "fdw_no_schemas";
	}break;
	case "HV00J":{
		return "fdw_option_name_not_found";
	}break;
	case "HV00K":{
		return "fdw_reply_handle";
	}break;
	case "HV00Q":{
		return "fdw_schema_not_found";
	}break;
	case "HV00R":{
		return "fdw_table_not_found";
	}break;
	case "HV00L":{
		return "fdw_unable_to_create_execution";
	}break;
	case "HV00M":{
		return "fdw_unable_to_create_reply";
	}break;
	case "HV00N":{
		return "fdw_unable_to_establish_connection";
	}break;
	case "P0000":{
		return "plpgsql_error";
	}break;
	case "P0001":{
		return "raise_exception";
	}break;
	case "P0002":{
		return "no_data_found";
	}break;
	case "P0003":{
		return "too_many_rows";
	}break;
	case "XX000":{
		return "internal_error";
	}break;
	case "XX001":{
		return "data_corrupted";
	}break;
	case "XX002":{
		return "index_corrupted";
	}break;
	default:{
		return "unknown_code";
	}break;
	}
}
