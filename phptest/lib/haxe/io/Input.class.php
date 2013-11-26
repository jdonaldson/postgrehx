<?php

class haxe_io_Input {
	public function __construct(){}
	public function readString($len) {
		$b = haxe_io_Bytes::alloc($len);
		$this->readFullBytes($b, 0, $len);
		return $b->toString();
	}
	public function readInt32() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$ch3 = $this->readByte();
		$ch4 = $this->readByte();
		return haxe_io_Input_0($this, $ch1, $ch2, $ch3, $ch4);
	}
	public function readInt16() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$n = haxe_io_Input_1($this, $ch1, $ch2);
		if(($n & 32768) !== 0) {
			return $n - 65536;
		}
		return $n;
	}
	public function readUntil($end) {
		$buf = new StringBuf();
		$last = null;
		while(($last = $this->readByte()) !== $end) {
			$buf->b .= _hx_string_or_null(chr($last));
		}
		return $buf->b;
	}
	public function read($nbytes) {
		$s = haxe_io_Bytes::alloc($nbytes);
		$p = 0;
		while($nbytes > 0) {
			$k = $this->readBytes($s, $p, $nbytes);
			if($k === 0) {
				throw new HException(haxe_io_Error::$Blocked);
			}
			$p += $k;
			$nbytes -= $k;
			unset($k);
		}
		return $s;
	}
	public function readFullBytes($s, $pos, $len) {
		while($len > 0) {
			$k = $this->readBytes($s, $pos, $len);
			$pos += $k;
			$len -= $k;
			unset($k);
		}
	}
	public function set_bigEndian($b) {
		$this->bigEndian = $b;
		return $b;
	}
	public function close() {
	}
	public function readBytes($s, $pos, $len) {
		$k = $len;
		$b = $s->b;
		if($pos < 0 || $len < 0 || $pos + $len > $s->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		while($k > 0) {
			$b[$pos] = chr($this->readByte());
			$pos++;
			$k--;
		}
		return $len;
	}
	public function readByte() {
		haxe_io_Input_2($this);
	}
	public $bigEndian;
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
	function __toString() { return 'haxe.io.Input'; }
}
function haxe_io_Input_0(&$__hx__this, &$ch1, &$ch2, &$ch3, &$ch4) {
	if($__hx__this->bigEndian) {
		return $ch4 | $ch3 << 8 | $ch2 << 16 | $ch1 << 24;
	} else {
		return $ch1 | $ch2 << 8 | $ch3 << 16 | $ch4 << 24;
	}
}
function haxe_io_Input_1(&$__hx__this, &$ch1, &$ch2) {
	if($__hx__this->bigEndian) {
		return $ch2 | $ch1 << 8;
	} else {
		return $ch1 | $ch2 << 8;
	}
}
function haxe_io_Input_2(&$__hx__this) {
	throw new HException("Not implemented");
}
