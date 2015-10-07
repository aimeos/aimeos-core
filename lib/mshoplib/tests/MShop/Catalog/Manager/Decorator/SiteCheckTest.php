<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Catalog\Manager\Decorator;


/**
 * Test class for \Aimeos\MShop\Catalog\Manager\Decorator\SiteCheck.
 */
class SiteCheckTest extends \PHPUnit_Framework_TestCase
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
		$context = \TestHelper::getContext();
		$this->object = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
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


	public function testGetTreePath()
	{
		$parent = $this->object->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$pathNodes = $this->object->getPath( $parent->getId() );

		if( ( $node = reset( $pathNodes ) ) === false ) {
			throw new \Exception( 'No node found' );
		}

		$this->assertEquals( $parent->getId(), $node->getId() );
	}


	public function testInsertMoveItem()
	{
		$item = $this->object->createItem();
		$parent = $this->object->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$this->object->insertItem( $item, $parent->getId() );
		$this->object->moveItem( $item->getId(), $parent->getId(), $parent->getId() );
		$savedItem = $this->object->getItem( $item->getId() );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( $item->getId(), $savedItem->getId() );
		$this->assertEquals( \TestHelper::getContext()->getEditor(), $savedItem->getEditor() );
	}
}
