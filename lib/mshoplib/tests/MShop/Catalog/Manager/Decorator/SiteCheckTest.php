<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Catalog_Manager_Decorator_SiteCheck.
 */
class MShop_Catalog_Manager_Decorator_SiteCheckTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Decorator_SiteCheckTest');
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
		$context = TestHelper::getContext();
		$this->_object = MShop_Catalog_Manager_Factory::createManager($context);
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


	public function testGetTreePath()
	{
		$parent = $this->_object->getTree(null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE);
		$pathNodes = $this->_object->getPath( $parent->getId() );

		if( ( $node = reset( $pathNodes ) ) === false ) {
			throw new Exception('No node found');
		}

		$this->assertEquals($parent->getId(), $node->getId());
	}


	public function testInsertMoveItem()
	{
		$item = $this->_object->createItem();
		$parent = $this->_object->getTree(null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE);

		$this->_object->insertItem($item, $parent->getId());
		$this->_object->moveItem($item->getId(), $parent->getId(), $parent->getId());
		$savedItem = $this->_object->getItem($item->getId());

		$this->_object->deleteItem($item->getId());

		$this->assertEquals($item->getId(), $savedItem->getId());
		$this->assertEquals(TestHelper::getContext()->getEditor(), $savedItem->getEditor());
	}
}
