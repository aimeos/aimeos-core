<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Standard_Order_Account_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		MShop_Factory::setCache( true );
		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Checkout_Standard_Order_Account_Standard( $this->context, $paths );
		$this->object->setView( TestHelper::getView() );
	}


	protected function tearDown()
	{
		MShop_Factory::clear();
		MShop_Factory::setCache( false );

		Controller_Frontend_Basket_Factory::createController( $this->context )->clear();
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
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$type = MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT;
		$manager = MShop_Customer_Manager_Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) === false ) {
			throw new Exception( 'No customer item found' );
		}

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setEmail( 'unittest@aimeos.org' );


		$mailStub = $this->getMockBuilder( 'MW_Mail_None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( 'MW_Mail_Message_None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$mailStub->expects( $this->once() )->method( 'send' );

		$this->context->setMail( $mailStub );


		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->context );
		$basketCntl->setAddress( $type, $addrItem );

		$view = TestHelper::getView();
		$view->orderBasket = $basketCntl->get();
		$this->context->setView( $view );
		$this->object->setView( $view );

		$orderBaseStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$customerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$orderBaseStub->expects( $this->once() )->method( 'saveItem' );
		$customerStub->expects( $this->once() )->method( 'saveItem' );

		MShop_Factory::injectManager( $this->context, 'customer', $customerStub );
		MShop_Factory::injectManager( $this->context, 'order/base', $orderBaseStub );

		$this->object->process();
	}
}
