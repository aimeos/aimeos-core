<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */


class Controller_ExtJS_JsonRpcTest extends MW_Unittest_Testcase
{
	protected static $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_JsonRpcTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	public static function setUpBeforeClass()
	{
		self::$_object = Controller_ExtJS_JsonRpc::getInstance( TestHelper::getContext() );
	}


	public static function tearDownAfterClass()
	{
		self::$_object = null;
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
		$this->_testdir = dirname( __FILE__ ) . $ds . '_testfiles' . $ds . 'jsonrpc' . $ds;
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


	public function testGetInstance()
	{
		$object = Controller_ExtJS_JsonRpc::getInstance( TestHelper::getContext() );
		$this->assertInstanceOf( 'Controller_ExtJS_JsonRpc', $object );
	}


	public function testGetInstanceInvalidName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object = Controller_ExtJS_JsonRpc::getInstance( TestHelper::getContext(), '$$$' );
	}


	public function testGetInstanceUnknown()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object = Controller_ExtJS_JsonRpc::getInstance( TestHelper::getContext(), 'Test' );
	}


	public function testGetJsonItemSchemas()
	{
		$result = self::$_object->getJsonItemSchemas();
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'Order_Base_Service_Attribute', $object );
	}


	public function testGetJsonItemSchemasInvalid()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testGetJsonSearchSchemas()
	{
		$result = self::$_object->getJsonSearchSchemas();
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'Order_Base_Service_Attribute', $object );
	}


	public function testGetJsonSearchSchemasInvalid()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}


	public function testGetJsonSmd()
	{
		$result = self::$_object->getJsonSmd( 'http://localhost' );
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

		$result = self::$_object->process( $params, '' );
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

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
	}


	public function testProcessRequestWrongParams()
	{
		$params = array(
			'method' => 'Product.searchItems',
			'params' => '',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
	}


	public function testProcessRequestWrongMethod()
	{
		$params = array(
			'method' => 'Product-searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
	}


	public function testProcessRequestInvalidControllerName()
	{
		$params = array(
			'method' => 'Pro-duct.searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
	}


	public function testProcessRequestUnknownController()
	{
		$params = array(
			'method' => 'Prod.searchItems',
			'params' => '{"site":"unittest","condition":{"&&":[{"~=":{"product.label":"Cafe"}}]},"start":0,"limit":10,"sort":"product.label","dir":"DESC"}',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
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

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		self::$_object->process( $params, '' );
	}


	public function testProcessJsonObject()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'object.txt' );
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
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'array.txt' );
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
		$result = self::$_object->process( array(), 'invalid' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonWrongFormat()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'invalid.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonWrongType()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'string.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoMethod()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'nomethod.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoParams()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'noparams.txt' );
		$object = json_decode( $result );

		$this->assertObjectHasAttribute( 'error', $object );
		$this->assertObjectHasAttribute( 'id', $object );
	}


	public function testProcessJsonNoId()
	{
		$ds = DIRECTORY_SEPARATOR;
		$result = self::$_object->process( array(), $this->_testdir . 'noid.txt' );
		$this->assertEquals( null, $result );
	}


	public function testProcessJsonNoResponse()
	{
		$this->markTestIncomplete( 'Not yet implemented' );
	}

}
