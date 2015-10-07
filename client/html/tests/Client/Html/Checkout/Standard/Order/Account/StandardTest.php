<?php

namespace Aimeos\Client\Html\Checkout\Standard\Order\Account;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );
		$this->context = \TestHelper::getContext();

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Order\Account\Standard( $this->context, $paths );
		$this->object->setView( \TestHelper::getView() );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::clear();
		\Aimeos\MShop\Factory::setCache( false );

		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertNotNull( $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No customer item found' );
		}

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setEmail( 'unittest@aimeos.org' );


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

		$this->context->setMail( $mailStub );


		$basketCntl = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$basketCntl->setAddress( $type, $addrItem );

		$view = \TestHelper::getView();
		$view->orderBasket = $basketCntl->get();
		$this->context->setView( $view );
		$this->object->setView( $view );

		$orderBaseStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$customerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$orderBaseStub->expects( $this->once() )->method( 'saveItem' );
		$customerStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'customer', $customerStub );
		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/base', $orderBaseStub );

		$this->object->process();
	}
}
