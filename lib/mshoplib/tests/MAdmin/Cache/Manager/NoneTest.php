<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\MAdmin\Cache\Manager;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MAdmin\Cache\Manager\None( $this->context );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MAdmin\\Cache\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'cache', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->object->searchItems( $search );

		$this->assertEquals( [], $results );
	}


	public function testGetItem()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Cache\\Exception' );
		$this->object->getItem( 'unittest' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$result = $this->object->saveItem( $item );
		$this->object->deleteItem( $item->getId() );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $result );
	}
}
