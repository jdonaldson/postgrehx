<?php

class sys_net_Socket {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->input = new sys_io_FileInput(null);
		$this->output = new sys_io_FileOutput(null);
		$this->protocol = "tcp";
	}}
	public function connect($host, $port) {
		$errs = null;
		$errn = null;
		$r = stream_socket_client(_hx_string_or_null($this->protocol) . "://" . _hx_string_or_null($host->_ip) . ":" . _hx_string_rec($port, ""), $errn, $errs);
		sys_net_Socket::checkError($r, $errn, $errs);
		$this->__s = $r;
		$this->assignHandler();
	}
	public function close() {
		fclose($this->__s);
		{
			$this->input->__f = null;
			$this->output->__f = null;
		}
		$this->input->close();
		$this->output->close();
	}
	public function assignHandler() {
		$this->input->__f = $this->__s;
		$this->output->__f = $this->__s;
	}
	public $output;
	public $input;
	public $protocol;
	public $__s;
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
	static function checkError($r, $code, $msg) {
		if(!($r === false)) {
			return;
		}
		throw new HException(haxe_io_Error::Custom("Error [" . _hx_string_rec($code, "") . "]: " . _hx_string_or_null($msg)));
	}
	function __toString() { return 'sys.net.Socket'; }
}
