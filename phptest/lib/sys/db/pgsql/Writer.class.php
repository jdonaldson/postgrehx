<?php

class sys_db_pgsql_Writer {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->bb = new haxe_io_BytesBuffer();
		$this->length_marker = -1;
		$this->pos = 0;
	}}
	public function getBytes() {
		$bytes = $this->bb->getBytes();
		if($this->length_marker !== -1) {
			sys_db_pgsql_ByteTools::setInt32($bytes, $this->length_marker, $bytes->length - $this->length_marker);
		}
		return $bytes;
	}
	public function addBytes($src, $pos, $len) {
		{
			if($pos < 0 || $len < 0 || $pos + $len > $src->length) {
				throw new HException(haxe_io_Error::$OutsideBounds);
			}
			$this->bb->b .= _hx_string_or_null(substr($src->b, $pos, $len));
		}
		$this->pos += $len;
		return $this;
	}
	public function addByte($byte) {
		$this->bb->b .= _hx_string_or_null(chr($byte));
		$this->pos += 1;
		return $this;
	}
	public function addObj($obj) {
		{
			$_g = 0; $_g1 = Reflect::fields($obj);
			while($_g < $_g1->length) {
				$k = $_g1[$_g];
				++$_g;
				$this->addCString($k);
				$this->addCString(Reflect::field($obj, $k));
				unset($k);
			}
		}
		$this->addByte(0);
		$this->pos += 1;
		return $this;
	}
	public function addMultiCString($strs) {
		{
			$_g = 0;
			while($_g < $strs->length) {
				$s = $strs[$_g];
				++$_g;
				$this->addCString($s);
				unset($s);
			}
		}
		$this->bb->b .= _hx_string_or_null(chr(0));
		$this->pos += 1;
		return $this;
	}
	public function addCString($str) {
		$b = haxe_io_Bytes::ofString($str);
		{
			$len = $b->length;
			if($len < 0 || $len > $b->length) {
				throw new HException(haxe_io_Error::$OutsideBounds);
			}
			$this->bb->b .= _hx_string_or_null(substr($b->b, 0, $len));
		}
		$this->bb->b .= _hx_string_or_null(chr(0));
		$this->pos += $b->length + 1;
		return $this;
	}
	public function addString($str) {
		$b = haxe_io_Bytes::ofString($str);
		{
			$len = $b->length;
			if($len < 0 || $len > $b->length) {
				throw new HException(haxe_io_Error::$OutsideBounds);
			}
			$this->bb->b .= _hx_string_or_null(substr($b->b, 0, $len));
		}
		$this->pos += $b->length;
		return $this;
	}
	public function msgLength() {
		$this->length_marker = $this->pos;
		$this->addInt32(0);
		$this->pos += 4;
		return $this;
	}
	public function addInt16($number) {
		$unsigned = sys_db_pgsql_Writer_0($this, $number);
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned / 255)));
		$unsigned &= 255;
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned)));
		$this->pos += 2;
		return $this;
	}
	public function addInt32($number) {
		$unsigned = sys_db_pgsql_Writer_1($this, $number);
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned / 16777215)));
		$unsigned &= 16777215;
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned / 65535)));
		$unsigned &= 65535;
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned / 255)));
		$unsigned &= 255;
		$this->bb->b .= _hx_string_or_null(chr(Math::floor($unsigned)));
		$this->pos += 4;
		return $this;
	}
	public $pos;
	public $length_marker;
	public $bb;
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
	function __toString() { return 'sys.db.pgsql.Writer'; }
}
function sys_db_pgsql_Writer_0(&$__hx__this, &$number) {
	if($number < 0) {
		return $number + 16777215;
	} else {
		return $number;
	}
}
function sys_db_pgsql_Writer_1(&$__hx__this, &$number) {
	if($number < 0) {
		return $number + 16777215;
	} else {
		return $number;
	}
}
