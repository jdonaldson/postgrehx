<?php

class EReg {
	public function __construct($r, $opt) {
		if(!php_Boot::$skip_constructor) {
		$this->pattern = $r;
		$a = _hx_explode("g", $opt);
		$this->{"global"} = $a->length > 1;
		if($this->{"global"}) {
			$opt = $a->join("");
		}
		$this->options = $opt;
		$this->re = '"' . str_replace('"','\\"',$r) . '"' . $opt;
	}}
	public function match($s) {
		$p = preg_match($this->re, $s, $this->matches, PREG_OFFSET_CAPTURE);
		if($p > 0) {
			$this->last = $s;
		} else {
			$this->last = null;
		}
		return $p > 0;
	}
	public $matches;
	public $re;
	public $options;
	public $pattern;
	public $global;
	public $last;
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
	function __toString() { return 'EReg'; }
}
