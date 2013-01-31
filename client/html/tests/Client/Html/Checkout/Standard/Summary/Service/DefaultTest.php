<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Summary_Service_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Checkout_Standard_Summary_Service_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Standard_Summary_Service_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		Controller_Frontend_Basket_Factory::createController( $this->_context )->clear();
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.provider', 'DirectDebit' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'Service item not found' );
		}

		$attributes = array(
			'payment.directdebit.accountowner' => 'test user',
			'payment.directdebit.accountnumber' => '1234567890',
			'payment.directdebit.bankcode' => '10305070',
			'payment.directdebit.bankname' => 'test credit institute',
		);

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->setService( 'payment', $service->getId(), $attributes );
		$controller->setService( 'delivery', $service->getId(), $attributes );

		$view = TestHelper::getView();
		$view->standardBasket = $controller->get();
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-summary-service">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	public function testIsCachable()
	{
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_BODY ) );
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_HEADER ) );
	}
}
