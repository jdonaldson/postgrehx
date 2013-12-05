<?php

class sys_db_PostgresConnection implements sys_db_Connection{
	public function __construct($params) {
		if(!php_Boot::$skip_constructor) {
		if($params->port === null) {
			$params->port = 5432;
		}
		$this->status = new haxe_ds_StringMap();
		$this->socket = new sys_net_Socket();
		$this->socket->input->set_bigEndian(true);
		$this->socket->output->set_bigEndian(true);
		$h = new sys_net_Host($params->host);
		$this->socket->connect($h, $params->port);
		$this->current_data_iterator = _hx_deref(new _hx_array(array()))->iterator();
		$this->current_complete_iterator = _hx_deref(new _hx_array(array()))->iterator();
		$this->writeMessage(sys_db_pgsql_ClientMessageType::StartupMessage(_hx_anonymous(array("user" => $params->user, "database" => $params->database))));
		while(true) {
			$ni = $this->readMessage();
			$__hx__t = ($ni);
			switch($__hx__t->index) {
			case 0:
			$ni1 = $__hx__t->params[0];
			{
				$__hx__t2 = ($ni1);
				switch($__hx__t2->index) {
				case 0:
				{
					null;
				}break;
				case 2:
				{
					$this->writeMessage(sys_db_pgsql_ClientMessageType::PasswordMessage($params->pass));
				}break;
				case 3:
				$salt = $__hx__t2->params[0];
				{
					$this->writeMessage(sys_db_pgsql_ClientMessageType::PasswordMessage(sys_db_PostgresConnection_0($this, $h, $ni, $ni1, $params, $salt)));
				}break;
				default:{
					throw new HException("Unimplemented: " . Std::string($ni1));
				}break;
				}
			}break;
			case 17:
			$args = $__hx__t->params[0];
			{
				$v = $args->value;
				$this->status->set($args->name, $v);
				$v;
			}break;
			case 1:
			$args = $__hx__t->params[0];
			{
				$this->process_id = $args->process_id;
				$this->secret_key = $args->secret_key;
			}break;
			case 18:
			$status = $__hx__t->params[0];
			{
				break 2;
			}break;
			default:{
				throw new HException("Unimplemented: " . Std::string($ni));
			}break;
			}
			unset($ni);
		}
	}}
	public function handleTag($tag) {
		$values = _hx_explode(" ", $tag);
		$command = $values->pop();
		switch($command) {
		case "INSERT":{
			$oid = Std::parseInt($values->pop());
			$rows = Std::parseInt($values->pop());
			if($rows === 1) {
				$this->last_insert_id = $oid;
			}
		}break;
		case "CREATE TABLE":{
			null;
		}break;
		}
	}
	public function readMessage() {
		while(true) {
			$ni = sys_db_pgsql_Messages::readMessage($this->socket);
			$__hx__t = ($ni);
			switch($__hx__t->index) {
			case 17:
			$args = $__hx__t->params[0];
			{
				$v = $args->value;
				$this->status->set($args->name, $v);
				$v;
			}break;
			case 12:
			$notice = $__hx__t->params[0];
			{
				$this->handleError($notice);
			}break;
			case 15:
			$notice = $__hx__t->params[0];
			{
				null;
			}break;
			default:{
				return $ni;
			}break;
			}
			unset($ni);
		}
	}
	public function writeMessage($msg) {
		sys_db_pgsql_Messages::writeMessage($this->socket, $msg);
	}
	public function rollback() {
		$this->request("ROLLBACK;");
	}
	public function commit() {
		$this->request("COMMIT;");
	}
	public function startTransaction() {
		$this->request("BEGIN;");
	}
	public function dbName() {
		return "PostgreSQL";
	}
	public function lastInsertId() {
		return $this->last_insert_id;
	}
	public function addValue($s, $v) {
		if($v === null || Std::is($v, _hx_qtype("Int"))) {
			$s->add($v);
		} else {
			if(Std::is($v, _hx_qtype("Bool"))) {
				$s->add((($v) ? 1 : 0));
			} else {
				$s->add($this->quote(Std::string($v)));
			}
		}
	}
	public function escape($s) {
		$s1 = _hx_explode("\x0A", $s)->join("\\n");
		$s2 = _hx_explode("\x0D", $s1)->join("\\r");
		$s3 = _hx_explode("\x09", $s2)->join("\\t");
		$s4 = _hx_explode("\\", $s3)->join("\\\\");
		$s5 = _hx_explode("'", $s4)->join("''");
		$s6 = _hx_explode("\x0C", $s5)->join("\\f");
		return $s6;
	}
	public function quote($s) {
		if($s === null) {
			return "NULL";
		}
		$s = _hx_explode("'", $s)->join("''");
		return "E'" . _hx_string_or_null($s) . "'";
	}
	public function close() {
		$this->socket->close();
	}
	public function handleError($notice) {
		while(true) {
			$ni = sys_db_pgsql_Messages::readMessage($this->socket);
			$__hx__t = ($ni);
			switch($__hx__t->index) {
			case 18:
			$status = $__hx__t->params[0];
			{
				break 2;
			}break;
			default:{
				throw new HException("Unexpected message " . Std::string($ni) . " in this state");
			}break;
			}
			unset($ni);
		}
		throw new HException($notice->message);
	}
	public function request($query) {
		$__hx__it = $this->current_complete_iterator;
		while($__hx__it->hasNext()) {
			$i = $__hx__it->next();
			null;
		}
		$this->writeMessage(sys_db_pgsql_ClientMessageType::Query($query));
		$this->current_complete_iterator = $this->getCommandCompletesIterator();
		$first_complete = $this->current_complete_iterator->next();
		$__hx__t = ($first_complete);
		switch($__hx__t->index) {
		case 0:
		{
			return new sys_db_PostgresResultSet(null, null);
		}break;
		case 1:
		$tag = $__hx__t->params[0];
		{
			$this->handleTag($tag);
			return new sys_db_PostgresResultSet(null, null);
		}break;
		case 2:
		$data_iterator = $__hx__t->params[1]; $row_description = $__hx__t->params[0];
		{
			return new sys_db_PostgresResultSet($row_description, $data_iterator);
		}break;
		}
	}
	public function getCommandCompletesIterator() {
		$_g = $this;
		$this->current_message = $this->readMessage();
		return _hx_anonymous(array("hasNext" => array(new _hx_lambda(array(&$_g), "sys_db_PostgresConnection_1"), 'execute'), "next" => array(new _hx_lambda(array(&$_g), "sys_db_PostgresConnection_2"), 'execute')));
	}
	public function getDataRowIterator() {
		$_g = $this;
		$this->current_message = $this->readMessage();
		return _hx_anonymous(array("hasNext" => array(new _hx_lambda(array(&$_g), "sys_db_PostgresConnection_3"), 'execute'), "next" => array(new _hx_lambda(array(&$_g), "sys_db_PostgresConnection_4"), 'execute')));
	}
	public $current_message;
	public $current_complete_iterator;
	public $current_data_iterator;
	public $last_insert_id;
	public $socket;
	public $secret_key;
	public $process_id;
	public $status;
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
	static function unexpectedMessage($msg) {
		throw new HException("Unexpected message " . Std::string($msg) . " in this state");
	}
	static function md5Auth($o) {
		return "md5" . _hx_string_or_null(haxe_crypto_Md5::encode(_hx_string_or_null(haxe_crypto_Md5::encode(_hx_string_or_null($o->pass) . _hx_string_or_null($o->user))) . _hx_string_or_null($o->salt)));
	}
	function __toString() { return 'sys.db.PostgresConnection'; }
}
function sys_db_PostgresConnection_0(&$__hx__this, &$h, &$ni, &$ni1, &$params, &$salt) {
	{
		$o = _hx_anonymous(array("pass" => $params->pass, "user" => $params->user, "salt" => $salt));
		return "md5" . _hx_string_or_null(haxe_crypto_Md5::encode(_hx_string_or_null(haxe_crypto_Md5::encode(_hx_string_or_null($o->pass) . _hx_string_or_null($o->user))) . _hx_string_or_null($o->salt)));
	}
}
function sys_db_PostgresConnection_1(&$_g) {
	{
		$__hx__t = ($_g->current_message);
		switch($__hx__t->index) {
		case 18:
		$status = $__hx__t->params[0];
		{
			return false;
		}break;
		default:{
			return true;
		}break;
		}
	}
}
function sys_db_PostgresConnection_2(&$_g) {
	{
		$__hx__it = $_g->current_data_iterator;
		while($__hx__it->hasNext()) {
			$r = $__hx__it->next();
			null;
		}
		$res = sys_db_PostgresConnection_5($_g);
		return $res;
	}
}
function sys_db_PostgresConnection_3(&$_g) {
	{
		$__hx__t = ($_g->current_message);
		switch($__hx__t->index) {
		case 10:
		$fields = $__hx__t->params[0];
		{
			return true;
		}break;
		case 4:
		$tag = $__hx__t->params[0];
		{
			return false;
		}break;
		default:{
			throw new HException("Unexpected message " . Std::string($_g->current_message) . " in this state");
		}break;
		}
		return false;
	}
}
function sys_db_PostgresConnection_4(&$_g) {
	{
		$res = new _hx_array(array());
		$__hx__t = ($_g->current_message);
		switch($__hx__t->index) {
		case 10:
		$fields = $__hx__t->params[0];
		{
			$res = $fields;
		}break;
		default:{
			throw new HException("Unexpected message " . Std::string($_g->current_message) . " in this state");
		}break;
		}
		$_g->current_message = $_g->readMessage();
		return $res;
	}
}
function sys_db_PostgresConnection_5(&$_g) {
	$__hx__t = ($_g->current_message);
	switch($__hx__t->index) {
	case 11:
	{
		$_g->current_message = $_g->readMessage();
		return sys_db_CommandComplete::$EmptyQueryResponse;
	}break;
	case 4:
	$tag = $__hx__t->params[0];
	{
		$_g->current_message = $_g->readMessage();
		return sys_db_CommandComplete::CommandComplete($tag);
	}break;
	case 19:
	$args = $__hx__t->params[0];
	{
		$_g->current_data_iterator = $_g->getDataRowIterator();
		return sys_db_CommandComplete::DataRows($args, $_g->current_data_iterator);
	}break;
	default:{
		throw new HException("Unexpected message " . Std::string($_g->current_message) . " in this state");
		return null;
	}break;
	}
}
