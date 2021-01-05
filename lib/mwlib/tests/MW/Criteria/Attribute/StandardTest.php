<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$func = function() {};

		$values = array(
			'type' => 'attribute_type',
			'internaltype' => 'internaltype',
			'code' => 'attribute_code',
			'internalcode' => 'internalcode',
			'internaldeps' => array( 'test' ),
			'label' => 'labelname',
			'default' => 'default value',
			'public' => false,
			'required' => false,
			'function' => $func,
		);

		$this->object = new \Aimeos\MW\Criteria\Attribute\Standard( $values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetType()
	{
		$this->assertEquals( 'attribute_type', $this->object->getType() );
	}


	public function testGetInternalType()
	{
		$this->assertEquals( 'internaltype', $this->object->getInternalType() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'attribute_code', $this->object->getCode() );
	}


	public function testGetInternalCode()
	{
		$this->assertEquals( 'internalcode', $this->object->getInternalCode() );
	}


	public function testGetInternalDeps()
	{
		$this->assertEquals( array( 'test' ), $this->object->getInternalDeps() );
	}


	public function testGetFunction()
	{
		$this->assertInstanceOf( \Closure::class, $this->object->getFunction() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'labelname', $this->object->getLabel() );
	}


	public function testGetDefault()
	{
		$this->assertEquals( 'default value', $this->object->getDefault() );
	}


	public function testIsPublic()
	{
		$this->assertEquals( false, $this->object->isPublic() );
	}


	public function testIsRequired()
	{
		$this->assertEquals( false, $this->object->isRequired() );
	}


	public function testToArray()
	{
		$expected = array(
			'code' => 'attribute_code',
			'type' => 'attribute_type',
			'label' => 'labelname',
			'public' => false,
			'default' => 'default value',
			'required' => false,
		);

		$this->assertEquals( $expected, $this->object->toArray() );
	}
}
