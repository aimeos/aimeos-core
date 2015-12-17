<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Warehouse;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$context = \TestHelperCntl::getContext();
		$this->object = new \Aimeos\Controller\Common\Product\Import\Csv\Cache\Warehouse\Standard( $context );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testGet()
	{
		$result = $this->object->get( 'default' );

		$this->assertNotEquals( null, $result );
	}


	public function testGetUnknown()
	{
		$this->assertEquals( null, $this->object->get( 'cache-test' ) );
	}


	public function testSet()
	{
		$item = \Aimeos\MShop\Factory::createManager( \TestHelperCntl::getContext(), 'product/stock/warehouse' )->createItem();
		$item->setCode( 'cache-test' );
		$item->setId( 1 );

		$this->object->set( $item );
		$id = $this->object->get( 'cache-test' );

		$this->assertEquals( $item->getId(), $id );
	}
}