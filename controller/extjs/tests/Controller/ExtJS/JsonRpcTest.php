<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_JsonRpcTest extends PHPUnit_Framework_TestCase
{
	private static $object;
	private $testdir;


	public static function setUpBeforeClass()
	{
		$cntlPaths = TestHelper::getControllerPaths();
		self::$object = new Controller_ExtJS_JsonRpc( TestHelper::getContext(), $cntlPaths );
	}


	public static function tearDownAfterClass()
	{
		self::$object = null;
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->testdir = dirname( __FILE__ ) . $ds . '_testfiles' . $ds . 'jsonrpc' . $ds;
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
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
		$result = self::$object->process( array(), $this->testdir . 'object.txt' );
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
		$result = self::$object->process( array(), $this->testdir . 'array.txt' );
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
		$result = self::$object->process( array(), $this->testdir . 'invalid.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonWrongType()
	{
		$result = self::$object->process( array(), $this->testdir . 'string.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoMethod()
	{
		$result = self::$object->process( array(), $this->testdir . 'nomethod.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoParams()
	{
		$result = self::$object->process( array(), $this->testdir . 'noparams.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoId()
	{
		$result = self::$object->process( array(), $this->testdir . 'noid.txt' );
		$this->assertEquals( null, $result );
	}


	public function testProcessJsonNoResponse()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}

}
