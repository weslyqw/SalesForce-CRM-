<?php
namespace Tests;

use WhatConverts\WhatConverts;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
	
	/** 
		* @test
		* @expectedException WhatConverts\Exception\WhatConvertsApiException 
	 */
 	public function it_throws_an_api_exception_when_whatconverts_returns_an_error_message()
 	{
 		$mock = new MockHandler([
		    new Response(200, ['X-Foo' => 'Bar'], file_get_contents(__DIR__ . '/fixtures/account_invalid.json'))
		]);
		$handler = HandlerStack::create($mock);
		$client = new WhatConverts(
			'97649-a5b2cdb272d759b4', 
			'6f55f2dd386a95a0de9a161308eae1bb',
			$handler
		);
		$nonExistentId = 70000000;
		$result = $client->getAccount($nonExistentId);
 	}

}