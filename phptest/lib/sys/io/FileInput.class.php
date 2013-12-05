<?php

class sys_io_FileInput extends haxe_io_Input {
	public function __construct($f) {
		if(!php_Boot::$skip_constructor) {
		$this->__f = $f;
	}}
	public function close() {
		parent::close();
		if($this->__f !== null) {
			fclose($this->__f);
		}
	}
	public function readBytes($s, $p, $l) {
		if(feof($this->__f)) {
			sys_io_FileInput_0($this, $l, $p, $s);
		}
		$r = fread($this->__f, $l);
		if(($r === false)) {
			sys_io_FileInput_1($this, $l, $p, $r, $s);
		}
		$b = haxe_io_Bytes::ofString($r);
		$s->blit($p, $b, 0, strlen($r));
		return strlen($r);
	}
	public function readByte() {
		if(feof($this->__f)) {
			sys_io_FileInput_2($this);
		}
		$r = fread($this->__f, 1);
		if(($r === false)) {
			sys_io_FileInput_3($this, $r);
		}
		return ord($r);
	}
	public $__f;
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
	static $__properties__ = array("set_bigEndian" => "set_bigEndian");
	function __toString() { return 'sys.io.FileInput'; }
}
function sys_io_FileInput_0(&$__hx__this, &$l, &$p, &$s) {
	throw new HException(new haxe_io_Eof());
}
function sys_io_FileInput_1(&$__hx__this, &$l, &$p, &$r, &$s) {
	throw new HException(haxe_io_Error::Custom("An error occurred"));
}
function sys_io_FileInput_2(&$__hx__this) {
	throw new HException(new haxe_io_Eof());
}
function sys_io_FileInput_3(&$__hx__this, &$r) {
	throw new HException(haxe_io_Error::Custom("An error occurred"));
}
