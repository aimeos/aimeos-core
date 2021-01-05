<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\DBSchema\Column;


class ItemTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Setup\DBSchema\Column\Item( 'testtable', 'testcol', 'varchar', '255', 'default', 'YES', 'utf8', 'utf8_general_ci' );
	}

	protected function tearDown() : void
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

	public function testGetCharset()
	{
		$this->assertEquals( 'utf8', $this->object->getCharset() );
	}

	public function testGetCollationType()
	{
		$this->assertEquals( 'utf8_general_ci', $this->object->getCollationType() );
	}

	public function testIsNullable()
	{
		$this->assertTrue( $this->object->isNullable() );

		$item = new \Aimeos\MW\Setup\DBSchema\Column\Item( '', '', '', 0, '', 'NO', '', '' );
		$this->assertFalse( $item->isNullable() );

		$this->expectException( \Aimeos\MW\Setup\Exception::class );
		new \Aimeos\MW\Setup\DBSchema\Column\Item( '', '', '', 0, '', '', '', '' );
	}
}
