<?php
require_once('src/simpleDB.php');
use DB\SimpleDB\SimpleDB;

class SimpleDBTest extends PHPUnit_Framework_TestCase
{
	public function testfirstTest()
	{
		$test= new SimpleDB;

		/**
		echo $test
				->select()
				->from('chair')
				->where(['name'=>'Rapheal','class'=>300])

				->result();
		**/
		echo $test->table('users',['id','username','check','oop'])
					->create('name',1)
					->save();	
	}
}