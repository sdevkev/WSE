<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageTest extends WebTestCase
{
	public function testShowPost()
	{
		$client = static::createClient();
		
		$client->request('GET', '/index');
		
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
	
	public function testShowPostCustomer()
	{
		$client = static::createClient();
		
		$client->request('GET', '/index#customer');
		
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
	
		public function testShowPostCart()
	{
		$client = static::createClient();
		
		$client->request('GET', '/index#cart');
		
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
}