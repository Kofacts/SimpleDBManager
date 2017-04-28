<?php 
namespace DB\SimpleDB;

class simpleDBManagerException extends Exception
{
	public function __construct()
	{
		throw new Exception;
	}
}