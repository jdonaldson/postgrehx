<?php

class Reflect {
	public function __construct(){}
	static function field($o, $field) {
		return _hx_field($o, $field);
	}
	static function fields($o) {
		if($o === null) {
			return new _hx_array(array());
		}
		return (($o instanceof _hx_array) ? new _hx_array(array('concat','copy','insert','iterator','length','join','pop','push','remove','reverse','shift','slice','sort','splice','toString','unshift')) : ((is_string($o)) ? new _hx_array(array('charAt','charCodeAt','indexOf','lastIndexOf','length','split','substr','toLowerCase','toString','toUpperCase')) : new _hx_array(_hx_get_object_vars($o))));
	}
	function __toString() { return 'Reflect'; }
}
