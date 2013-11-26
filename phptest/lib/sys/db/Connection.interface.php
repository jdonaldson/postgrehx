<?php

interface sys_db_Connection {
	function quote($s);
	function close();
	function request($s);
}
