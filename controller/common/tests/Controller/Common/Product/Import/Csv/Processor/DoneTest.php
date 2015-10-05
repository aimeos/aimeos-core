<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Processor;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class DoneTest extends \PHPUnit_Framework_TestCase
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
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelper::getContext();
		$this->object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, array() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

		$this->object = null;
	}


	public function testProcess()
	{
		$product = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->createItem();

		$result = $this->object->process( $product, array( 'test' ) );

		$this->assertEquals( array( 'test' ), $result );
	}
}