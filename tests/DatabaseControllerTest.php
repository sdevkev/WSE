<?php
// tests/DatabaseControllerTest.php
namespace App\Controller;

use PHPUnit\Framework\TestCase;

class DatabaseControllerTest extends TestCase
{
	public function testLogin()
	{
		$id=0;
		$dbcontroller = new DatabaseController();
		$result = $dbcontroller->Login();
		$id = $result->getID();
		
		$this->assertEquals(1, $id);
	}
	
	
	public function testCancelOrder()
	{
		$dbcontroller2 = new DatabaseController();
		$dbcontroller2->$orderid = 10;
		$result2 = $dbcontroller->CancelOrder();
		
		
		$this->assertEquals("order deleted", $result2);
	}
	
	



}