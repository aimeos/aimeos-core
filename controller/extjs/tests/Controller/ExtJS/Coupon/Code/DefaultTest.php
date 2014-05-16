<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Coupon_Code_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Coupon_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Coupon_Code_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'coupon.code.code' => 'OPQR' ) ),
					1 => array( '==' => (object) array( 'coupon.code.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'coupon.code.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( '2000000', $result['items'][0]->{'coupon.code.count'} );
	}


	public function testSaveDeleteItem()
	{
		$couponManager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $couponManager->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.label', 'Unit test example' ) );
		$result = $couponManager->searchItems( $search );

		if( ( $couponItem = reset( $result ) ) === false ) {
			throw new Exception( 'No coupon item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'coupon.code.couponid' => $couponItem->getId(),
				'coupon.code.code' => 'zzzz',
				'coupon.code.count' => -1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'coupon.code.code' => 'zzzz' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array($saved['items']->{'coupon.code.id'}) );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'coupon.code.id'} );
		$this->assertEquals( $saved['items']->{'coupon.code.couponid'}, $searched['items'][0]->{'coupon.code.couponid'} );
		$this->assertEquals( $saved['items']->{'coupon.code.code'}, $searched['items'][0]->{'coupon.code.code'} );
		$this->assertEquals( $saved['items']->{'coupon.code.count'}, $searched['items'][0]->{'coupon.code.count'} );
		$this->assertEquals( $saved['items']->{'coupon.code.datestart'}, $searched['items'][0]->{'coupon.code.datestart'} );
		$this->assertEquals( $saved['items']->{'coupon.code.dateend'}, $searched['items'][0]->{'coupon.code.dateend'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testAbstractInit()
	{
		$expected = array('success' => true);
		$actual = $this->_object->init( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array('success' => true);
		$actual = $this->_object->finish( new stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->_object->getItemSchema();
		$expected = array(
			'name' => 'Coupon_Code',
			'properties' => array(
				'coupon.code.id' => array(
					'description' => 'Coupon code ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.siteid' => array(
					'description' => 'Coupon code site ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.couponid' => array(
					'description' => 'Coupon ID',
					'optional' => false,
					'type' => 'integer',
				),
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->_object->getSearchSchema();
		$expected = array(
			'criteria' => array(
				'coupon.code.code' => array(
					'description' => 'Coupon code value',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.count' => array(
					'description' => 'Coupon code quantity',
					'optional' => false,
					'type' => 'string',
				),
				'coupon.code.datestart' => array(
					'description' => 'Coupon code start date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.dateend' => array(
					'description' => 'Coupon code end date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.ctime' => array(
					'description' => 'Coupon code create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.mtime' => array(
					'description' => 'Coupon code modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'coupon.code.editor' => array(
					'description' => 'Coupon code editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}
}