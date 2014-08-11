<?php

/**
 * Test class for MW_Tree_Node_DBNestedSet.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Tree_Node_DBNestedSetTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$child1 = new MW_Tree_Node_DBNestedSet( array( 'id' => null, 'label' => 'child1', 'status' => '0', 'left' => 2, 'right' => 3 ) );
		$child2 = new MW_Tree_Node_DBNestedSet( array( 'id' => null, 'label' => 'child2', 'status' => '1', 'left' => 4, 'right' => 5 ) );

		$this->_object = new MW_Tree_Node_DBNestedSet( array( 'id' => 1, 'label' => 'parent', 'status' => '1', 'left' => 1, 'right' => 6 ), array( $child1, $child2 ) );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}

	public function testHasChildren()
	{
		$this->assertEquals( true, $this->_object->hasChildren() );
	}

	public function testHasNoChildren()
	{
		$tree = new MW_Tree_Node_DBNestedSet( array( 'id' => 1, 'label' => 'parent', 'status' => '1', 'left' => 1, 'right' => 2 ) );
		$this->assertEquals( false, $tree->hasChildren() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals( true, $this->_object->isModified() );
	}

	public function testGetChild()
	{
		$this->setExpectedException('MW_Tree_Exception');
		$this->_object->getChild(null);
	}

	public function testMagicGet()
	{
		$this->setExpectedException('MW_Tree_Exception');
		$test = $this->_object->notDefined;
	}

}
