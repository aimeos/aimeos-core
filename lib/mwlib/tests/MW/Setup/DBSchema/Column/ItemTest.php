<?php

/**
 * Test class for MW_Tree_Node_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_DBSchema_Column_ItemTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new MW_Setup_DBSchema_Column_Item( 'testtable', 'testcol', 'varchar', '255', 'default', 'YES', 'utf8_bin' );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}

	public function testGetDataType()
	{
		$this->assertEquals( 'varchar', $this->object->getDataType() );
	}

	public function testGetDefaultValue()
	{
		$this->assertEquals( 'default', $this->object->getDefaultValue() );
	}

	public function testGetMaxLength()
	{
		$this->assertEquals( 255, $this->object->getMaxLength() );
	}

	public function testGetName()
	{
		$this->assertEquals( 'testcol', $this->object->getName() );
	}

	public function testGetTableName()
	{
		$this->assertEquals( 'testtable', $this->object->getTableName() );
	}

	public function testGetCollationType()
	{
		$this->assertEquals( 'utf8_bin', $this->object->getCollationType() );
	}

	public function testIsNullable()
	{
		$this->assertTrue( $this->object->isNullable() );

		$item = new MW_Setup_DBSchema_Column_Item( '', '', '', 0, '', 'NO', '' );
		$this->assertFalse( $item->isNullable() );

		$this->setExpectedException('MW_Setup_Exception');
		new MW_Setup_DBSchema_Column_Item( '', '', '', 0, '', '', '' );
	}
}
