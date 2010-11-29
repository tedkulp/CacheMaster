<?php

require_once(dirname(dirname(dirname(__FILE__))).'/lib/adodb/adodb.inc.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/adodb/drivers/adodb-mysql.inc.php');

class cachemaster_mysql extends adodb_mysql
{
	var $rsPrefix = 'cachemaster_rs_';
	var $memCache = true;
	var $memCacheHost = 'localhost';
	
	function &SelectLimit($sql, $nrows=-1, $offset=-1, $inputarr=false, $secs=0)
	{
		return parent::SelectLimit($sql, $nrows, $offset, $inputarr, 180);
	}
	
	function GetCol($sql, $inputarr = false, $trim = false)
	{
		$arr =& parent::CacheGetCol(180, $sql, $inputarr, $trim);
		return $arr;
	}
	
	function &GetAll($sql, $inputarr=false)
	{
		$arr =& parent::CacheGetArray(180, $sql, $inputarr);
		return $arr;
	}
	
	function GetRow($sql,$inputarr=false)
	{
		$arr =& parent::CacheGetRow(180, $sql, $inputarr);
		return $arr;
	}
	
	function GetArray($sql,$inputarr=false)
	{
		$arr =& parent::CacheGetArray(180, $sql, $inputarr);
		return $arr;
	}
	
	function &Execute($sql,$inputarr=false) 
	{
		$backtrace = debug_backtrace();
		if (strpos(strtolower($sql), 'select') === false || $backtrace[1]['function'] == 'CacheExecute')
			return parent::Execute($sql, $inputarr);
		
		$arr =& parent::CacheExecute(180, $sql, $inputarr);
		return $arr;
	}
}

class cachemaster_rs_mysql extends ADORecordSet_mysql
{
	
}

global $ADODB_NEWCONNECTION;
$ADODB_NEWCONNECTION = 'cachemaster_factory';

function& cachemaster_factory($driver)
{
	if ($driver !== 'mysql') return false;
	$driver = 'cachemaster_'.$driver;
	$obj = new $driver();
	return $obj;
}
