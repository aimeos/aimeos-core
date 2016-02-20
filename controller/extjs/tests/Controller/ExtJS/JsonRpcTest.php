<?php

namespace Aimeos\Controller\ExtJS;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class JsonRpcTest extends \PHPUnit_Framework_TestCase
{
	private static $object;
	private $testdir;


	public static function setUpBeforeClass()
	{
		$cntlPaths = \TestHelperExtjs::getControllerPaths();
		self::$object = new \Aimeos\Controller\ExtJS\JsonRpc( \TestHelperExtjs::getContext(), $cntlPaths );
	}


	public static function tearDownAfterClass()
	{
		self::$object = null;
	}


	public function testGetJsonItemSchemas()
	{
		$result = self::$object->getJsonItemSchemas();
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'Order_Base_Service_Attribute', $object );
	}


	public function testGetJsonItemSchemasInvalid()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testGetJsonSearchSchemas()
	{
		$result = self::$object->getJsonSearchSchemas();
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'Order_Base_Service_Attribute', $object );
	}


	public function testGetJsonSearchSchemasInvalid()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testGetJsonSmd()
	{
		$result = self::$object->getJsonSmd( 'http://localhost' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'target', $object );
		$this->assertEquals( 'http://localhost', $object->{'target'} );

		$this->assertObjectHasAttribute( 'services', $object );
		$this->assertObjectHasAttribute( 'Order_Base_Service_Attribute.deleteItems', $object->{'services'} );
	}


	public function testGetJsonSmdInvalid()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testProcessRequestWithResult()
	{
		$params = array(
			'method' => 'Product.searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'items', $object );
		$this->assertObjectHasAttribute( 'total', $object );
		$this->assertObjectHasAttribute( 'success', $object );
		$this->assertEquals( true, $object->{'success'} );
	}


	public function testProcessRequestWithoutResult()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testProcessRequestNoParams()
	{
		$params = array(
			'method' => 'Product.searchItems',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessRequestWrongParams()
	{
		$params = array(
			'method' => 'Product.searchItems',
			'params' => '',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessRequestWrongMethod()
	{
		$params = array(
			'method' => 'Product-searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessRequestInvalidControllerName()
	{
		$params = array(
			'method' => 'Pro-duct.searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessRequestUnknownController()
	{
		$params = array(
			'method' => 'Prod.searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessRequestInvalidController()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testProcessRequestUnknownMethod()
	{
		$params = array(
			'method' => 'Product.test',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$result = self::$object->process( $params, '' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
	}


	public function testProcessJsonObject()
	{
		$content = '{"jsonrpc":"2.0","method":"Product.searchItems","params":{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"},"id":1}';
		$result = self::$object->process( array(), $content );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'jsonrpc', $object );
		$this->assertEquals( '2.0', $object->{'jsonrpc'} );

		$this->assertObjectHasAttribute( 'id', $object );
		$this->assertEquals( 1, $object->{'id'} );

		$this->assertObjectHasAttribute( 'result', $object );
		$this->assertObjectHasAttribute( 'items', $object->{'result'} );
		$this->assertObjectHasAttribute( 'total', $object->{'result'} );
		$this->assertObjectHasAttribute( 'success', $object->{'result'} );

		$this->assertEquals( 2, $object->{'result'}->{'total'} );
		$this->assertEquals( true, $object->{'result'}->{'success'} );
	}


	public function testProcessJsonArray()
	{
		$content = '[{"jsonrpc":"2.0","method":"Product.searchItems","params":{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"},"id":1}, {"jsonrpc":"2.0","method":"Product_Type.searchItems","params":{"site":"unit","start":0,"limit":2,"sort":"product.type.code","dir":"DESC"},"id":2}]';
		$result = self::$object->process( array(), $content );
		$list = json_decode( $result );

		$this->assertEquals( 2, count( $list ) );

		$this->assertObjectHasAttribute( 'jsonrpc', $list[0] );
		$this->assertEquals( '2.0', $list[0]->{'jsonrpc'} );

		$this->assertObjectHasAttribute( 'id', $list[0] );
		$this->assertEquals( 1, $list[0]->{'id'} );

		$this->assertObjectHasAttribute( 'jsonrpc', $list[1] );
		$this->assertEquals( '2.0', $list[1]->{'jsonrpc'} );

		$this->assertObjectHasAttribute( 'id', $list[1] );
		$this->assertEquals( 2, $list[1]->{'id'} );
	}


	public function testProcessJsonWrongInputstream()
	{
		$result = self::$object->process( array(), 'invalid' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonWrongFormat()
	{
		$result = self::$object->process( array(), '{]}[' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonWrongType()
	{
		$result = self::$object->process( array(), '"string"' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoMethod()
	{
		$content = '{"jsonrpc":"2.0","params":{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"},"id":1}';
		$result = self::$object->process( array(), $content );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoParams()
	{
		$content = '{"jsonrpc":"2.0","method":"Product.searchItems","id":1}';
		$result = self::$object->process( array(), $content );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoId()
	{
		$content = '{"jsonrpc":"2.0","method":"Product.searchItems","params":{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}}';
		$result = self::$object->process( array(), $content );
		$this->assertEquals( null, $result );
	}


	public function testProcessJsonNoResponse()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}

}
