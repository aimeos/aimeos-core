<?php

namespace Aimeos\MW\Criteria\Attribute;


/**
 * Test class for \Aimeos\MW\Criteria\Attribute\Standard.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    \Aimeos\MW\Criteria\Attribute\Standard
	 * @access protected
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
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
		);

		$this->object = new \Aimeos\MW\Criteria\Attribute\Standard($values);
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


	public function testGetType()
	{
		$this->assertEquals('attribute_type', $this->object->getType());
	}


	public function testGetInternalType()
	{
		$this->assertEquals('internaltype', $this->object->getInternalType());
	}

	public function testGetCode()
	{
		$this->assertEquals('attribute_code', $this->object->getCode());
	}

	public function testGetInternalCode()
	{
		$this->assertEquals('internalcode', $this->object->getInternalCode());
	}

	public function testGetInternalDeps()
	{
		$this->assertEquals(array( 'test' ), $this->object->getInternalDeps());
	}

	public function testGetLabel()
	{
		$this->assertEquals('labelname', $this->object->getLabel());
	}

	public function testGetDefault()
	{
		$this->assertEquals('default value', $this->object->getDefault());
	}

	public function testIsPublic()
	{
		$this->assertEquals(false, $this->object->isPublic());
	}

	public function testIsRequired()
	{
		$this->assertEquals(false, $this->object->isRequired());
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

		$this->assertEquals($expected, $this->object->toArray());
	}
}
