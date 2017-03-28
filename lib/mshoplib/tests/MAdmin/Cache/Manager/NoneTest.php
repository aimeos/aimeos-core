<?php

namespace Aimeos\MAdmin\Cache\Manager;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class NoneTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MAdmin\Cache\Manager\None( $this->context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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
		$this->object->saveItem( $item );
		$this->object->deleteItem( $item->getId() );
	}
}
