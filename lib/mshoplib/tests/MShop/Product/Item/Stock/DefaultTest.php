<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


/**
 * Test class for MShop_Product_Item_Stock_Default.
 */
class MShop_Product_Item_Stock_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Product_Item_Stock_Default
	 * @access protected
	 */
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Product_Item_Stock_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 66,
			'siteid'=>99,
			'prodid' => 46677,
			'warehouseid' => 44,
			'stocklevel' => 1000,
			'backdate' => '2010-01-01 11:55:00',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Product_Item_Stock_Default( $this->_values );
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

	public function testGetId()
	{
		$this->assertEquals( $this->_values['id'], $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertNull( $this->_object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( $this->_values['prodid'], $this->_object->getProductId() );
	}

	public function testSetProductId()
	{
		$this->_object->setProductId(10000);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 10000, $this->_object->getProductId() );
	}

	public function testGetWarehouseId()
	{
		$this->assertEquals( $this->_values['warehouseid'], $this->_object->getWarehouseId() );
	}

	public function testSetWarehouseId()
	{
		$this->_object->setWarehouseId(30000);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 30000, $this->_object->getWarehouseId() );
	}

	public function testGetStocklevel()
	{
		$this->assertEquals( $this->_values['stocklevel'], $this->_object->getStocklevel() );
	}

	public function testSetStocklevel()
	{
		$this->_object->setStocklevel(200);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 200, $this->_object->getStocklevel() );
	}

	public function testGetDateBack()
	{
		$this->assertEquals( $this->_values['backdate'], $this->_object->getDateBack() );
	}

	public function testSetDateBack()
	{
		$this->_object->setDateBack('2010-10-10 01:10:00');
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( '2010-10-10 01:10:00', $this->_object->getDateBack() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['product.stock.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['product.stock.siteid'] );
		$this->assertEquals( $this->_object->getProductId(), $arrayObject['product.stock.productid'] );
		$this->assertEquals( $this->_object->getWarehouseId(), $arrayObject['product.stock.warehouseid'] );
		$this->assertEquals( $this->_object->getStocklevel(), $arrayObject['product.stock.stocklevel'] );
		$this->assertEquals( $this->_object->getDateBack(), $arrayObject['product.stock.dateback'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['product.stock.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['product.stock.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['product.stock.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}
