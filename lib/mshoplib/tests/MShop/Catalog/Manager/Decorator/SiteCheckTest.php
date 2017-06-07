<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\MShop\Catalog\Manager\Decorator;


class SiteCheckTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$this->object = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetTreePath()
	{
		$parent = $this->object->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
		$pathNodes = $this->object->getPath( $parent->getId() );

		if( ( $node = reset( $pathNodes ) ) === false ) {
			throw new \RuntimeException( 'No node found' );
		}

		$this->assertEquals( $parent->getId(), $node->getId() );
	}


	public function testInsertMoveItem()
	{
		$item = $this->object->createItem();
		$parent = $this->object->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$resultInsert = $this->object->insertItem( $item, $parent->getId() );
		$this->object->moveItem( $item->getId(), $parent->getId(), $parent->getId() );
		$savedItem = $this->object->getItem( $item->getId() );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( $item->getId(), $savedItem->getId() );
		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $resultInsert );
		$this->assertEquals( \TestHelperMShop::getContext()->getEditor(), $savedItem->getEditor() );
	}
}
