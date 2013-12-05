<?php

class haxe_io_Bytes {
	public function __construct($length, $b) {
		if(!php_Boot::$skip_constructor) {
		$this->length = $length;
		$this->b = $b;
	}}
	public function toString() {
		return $this->b;
	}
	public function readString($pos, $len) {
		if($pos < 0 || $len < 0 || $pos + $len > $this->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		return substr($this->b, $pos, $len);
	}
	public function blit($pos, $src, $srcpos, $len) {
		if($pos < 0 || $srcpos < 0 || $len < 0 || $pos + $len > $this->length || $srcpos + $len > $src->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		$this->b = substr($this->b, 0, $pos) . substr($src->b, $srcpos, $len) . substr($this->b, $pos+$len);
	}
	public $b;
	public $length;
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
	static function alloc($length) {
		return new haxe_io_Bytes($length, str_repeat(chr(0), $length));
	}
	static function ofString($s) {
		return new haxe_io_Bytes(strlen($s), $s);
	}
	function __toString() { return $this->toString(); }
}
