<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Tree\Node;


class DBNestedSetTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$child1 = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => null, 'label' => 'child1', 'status' => '0', 'left' => 2, 'right' => 3 ) );
		$child2 = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => null, 'label' => 'child2', 'status' => '1', 'left' => 4, 'right' => 5 ) );

		$this->object = new \Aimeos\MW\Tree\Node\DBNestedSet( array( 'id' => 1, 'label' => 'parent', 'status' => '1', 'left' => 1, 'right' => 6 ), array( $child1, $child2 ) );
	}


	protected function tearDown() : void
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
		$this->object->setId( null );
		$this->assertEquals( true, $this->object->isModified() );
	}
}
