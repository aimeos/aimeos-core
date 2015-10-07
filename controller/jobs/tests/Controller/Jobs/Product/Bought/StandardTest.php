<?php

namespace Aimeos\Controller\Jobs\Product\Bought;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $aimeos;


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
		$this->aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Product\Bought\Standard( $this->context, $this->aimeos );
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


	public function testGetName()
	{
		$this->assertEquals( 'Products bought together', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Creates bought together product suggestions';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Lists\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'deleteItems', 'saveItem' ) )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'product/lists', $stub );

		$stub->expects( $this->atLeastOnce() )->method( 'deleteItems' );
		$stub->expects( $this->atLeastOnce() )->method( 'saveItem' );

		$this->object->run();
	}
}