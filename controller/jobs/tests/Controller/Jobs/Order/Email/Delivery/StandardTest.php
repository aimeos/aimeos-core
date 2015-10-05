<?php

namespace Aimeos\Controller\Jobs\Order\Email\Delivery;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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

		$this->object = new \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard( $context, $aimeos );
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
		$this->assertEquals( 'Order delivery related e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends order delivery status update e-mails';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();


		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\Message\\None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$mailStub->expects( $this->once() )->method( 'send' );

		$context->setMail( $mailStub );


		$orderAddressItem = \Aimeos\MShop\Order\Manager\Factory::createManager( $context )
			->getSubManager( 'base' )->getSubManager( 'address' )->createItem();


		$name = 'ControllerJobsEmailDeliveryDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );

		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'searchItems', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setMethods( array( 'saveItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setMethods( array( 'load' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );


		$orderItem = new \Aimeos\MShop\Order\Item\Standard( array( 'ctime' => '2000-01-01 00:00:00' ) );
		$orderBaseItem = $orderBaseManagerStub->createItem();
		$orderBaseItem->setAddress( $orderAddressItem );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array(), array(), array() ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( $orderBaseItem ) );

		$orderStatusManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard( $context, $aimeos );
		$object->run();
	}


	public function testRunException()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();


		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$context->setMail( $mailStub );


		$name = 'ControllerJobsEmailDeliveryDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();


		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array(), array(), array() ) );


		$object = new \Aimeos\Controller\Jobs\Order\Email\Delivery\Standard( $context, $aimeos );
		$object->run();
	}

}
