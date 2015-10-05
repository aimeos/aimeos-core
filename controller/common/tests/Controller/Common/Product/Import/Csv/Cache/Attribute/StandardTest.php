<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Cache\Attribute;


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

		$context = \TestHelper::getContext();
		$this->object = new \Aimeos\Controller\Common\Product\Import\Csv\Cache\Attribute\Standard( $context );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testGet()
	{
		$item = $this->object->get( 'black', 'color' );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Attribute\\Item\\Iface', $item );
		$this->assertEquals( 'black', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}


	public function testGetUnknown()
	{
		$this->assertEquals( null, $this->object->get( 'cache-test', 'color' ) );
	}


	public function testSet()
	{
		$item = $this->object->get( 'black', 'color' );
		$item->setCode( 'cache-test' );

		$this->object->set( $item );
		$item = $this->object->get( 'cache-test', 'color' );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Attribute\\Item\\Iface', $item );
		$this->assertEquals( 'cache-test', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}
}