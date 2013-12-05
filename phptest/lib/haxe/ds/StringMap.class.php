<?php

class haxe_ds_StringMap implements IMap, IteratorAggregate{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->h = array();
	}}
	public function getIterator() {
		return $this->iterator();
	}
	public function iterator() {
		return new _hx_array_iterator(array_values($this->h));
	}
	public function set($key, $value) {
		$this->h[$key] = $value;
	}
	public $h;
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
	function __toString() { return 'haxe.ds.StringMap'; }
}
