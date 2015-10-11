<?php

namespace Aimeos\Controller\Jobs\Order\Cleanup\Unfinished;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest
	extends \PHPUnit_Framework_TestCase
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
		$aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Order\Cleanup\Unfinished\Standard( $context, $aimeos );
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


	public function testGetName()
	{
		$this->assertEquals( 'Removes unfinished orders', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Deletes unfinished orders an makes their products and coupon codes available again';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();


		$name = 'ControllerJobsOrderCleanupUnfinishedDefaultRun';
		$context->getConfig()->set( 'mshop/order/manager/name', $name );
		$context->getConfig()->set( 'controller/common/order/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'searchItems', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Order\\Standard' )
			->setMethods( array( 'unblock' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();


		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );
		\Aimeos\Controller\Common\Order\Factory::injectController( '\\Aimeos\\Controller\\Common\\Order\\' . $name, $orderCntlStub );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setBaseId( 1 );
		$orderItem->setId( 2 );


		$orderManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderItem->getId() => $orderItem ) ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'deleteItems' );

		$orderCntlStub->expects( $this->once() )->method( 'unblock' );


		$object = new \Aimeos\Controller\Jobs\Order\Cleanup\Unfinished\Standard( $context, $aimeos );
		$object->run();
	}
}
