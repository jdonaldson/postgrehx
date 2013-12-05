<?php

class Sys {
	public function __construct(){}
	static function getEnv($s) {
		return getenv($s);
	}
	function __toString() { return 'Sys'; }
}
