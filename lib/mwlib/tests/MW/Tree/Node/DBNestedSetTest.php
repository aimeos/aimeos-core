<?php

namespace Aimeos\MW\Tree\Node;


/**
 * Test class for \Aimeos\MW\Tree\Node\DBNestedSet.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class DBNestedSetTest extends \PHPUnit_Framework_TestCase
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
		$child1 = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => null, 'label' => 'child1', 'status' => '0', 'left' => 2, 'right' => 3 ) );
		$child2 = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => null, 'label' => 'child2', 'status' => '1', 'left' => 4, 'right' => 5 ) );

		$this->object = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => 1, 'label' => 'parent', 'status' => '1', 'left' => 1, 'right' => 6 ), array( $child1, $child2 ) );
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

	public function testHasChildren()
	{
		$this->assertEquals( true, $this->object->hasChildren() );
	}

	public function testHasNoChildren()
	{
		$tree = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => 1, 'label' => 'parent', 'status' => '1', 'left' => 1, 'right' => 2 ) );
		$this->assertEquals( false, $tree->hasChildren() );
	}

	public function testSetId()
	{
		$this->object->setId(null);
		$this->assertEquals( true, $this->object->isModified() );
	}

	public function testGetChild()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Tree\\Exception');
		$this->object->getChild(null);
	}

	public function testMagicGet()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Tree\\Exception');
		$this->object->notDefined;
	}

}
