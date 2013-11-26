<?php

class Lambda {
	public function __construct(){}
	static function hlist($it) {
		$l = new HList();
		if(null == $it) throw new HException('null iterable');
		$__hx__it = $it->iterator();
		while($__hx__it->hasNext()) {
			$i = $__hx__it->next();
			$l->add($i);
		}
		return $l;
	}
	function __toString() { return 'Lambda'; }
}
