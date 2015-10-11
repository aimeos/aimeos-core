<?php

namespace Aimeos\Client\Html\Checkout\Standard\Address\Delivery;


/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->context = \TestHelper::getContext();

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Address\Delivery\Standard( $this->context, $paths );
		$this->object->setView( \TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
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
		$view = $this->object->getView();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery">', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $view->deliveryOptional ) );
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
		$this->object->process();
	}


	public function testProcessNewAddress()
	{
		$view = \TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'delivery' )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = \TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 2, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->deliveryError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->deliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessNewAddressUnknown()
	{
		$view = \TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
				'order.base.address.flag' => '1',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->get();
		$this->assertEquals( 0, $basket->getAddress( 'delivery' )->getFlag() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = \TestHelper::getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20AB',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$this->assertEquals( 1, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->deliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessAddressDelete()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No customer address found' );
		}

		$item->setId( null );
		$manager->saveItem( $item );

		$view = \TestHelper::getView();
		$this->context->setUserId( $item->getRefId() );

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$manager->getItem( $item->getId() );
	}


	public function testProcessAddressDeleteUnknown()
	{
		$view = \TestHelper::getView();

		$param = array( 'ca_delivery_delete' => '-1' );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->process();
	}


	public function testProcessAddressDeleteNoLogin()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No customer address found' );
		}

		$view = \TestHelper::getView();

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->process();
	}


	public function testProcessExistingAddress()
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \Exception( 'Customer item not found' );
		}

		$customerAddressManager = $customerManager->getSubManager( 'address' );
		$search = $customerAddressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.refid', $customer->getId() ) );
		$result = $customerAddressManager->searchItems( $search );

		if( ( $address = reset( $result ) ) === false ) {
			throw new \Exception( 'Customer address item not found' );
		}

		$this->context->setUserId( $customer->getId() );

		$view = \TestHelper::getView();

		$param = array( 'ca_deliveryoption' => $address->getId() );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$this->context->setEditor( null );
		$basket = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'delivery' )->getCompany() );
	}


	public function testProcessInvalidId()
	{
		$view = \TestHelper::getView();

		$param = array( 'ca_deliveryoption' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->process();
	}
}
