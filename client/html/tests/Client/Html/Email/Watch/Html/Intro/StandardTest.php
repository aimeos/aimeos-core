<?php

namespace Aimeos\Client\Html\Email\Watch\Html\Intro;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private static $customerItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( \TestHelper::getContext() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( self::$customerItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No customer found' );
		}
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$this->emailMock = $this->getMock( '\\Aimeos\\MW\\Mail\\Message\\None' );

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Email\Watch\Html\Intro\Standard( $this->context, $paths );

		$view = \TestHelper::getView();
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->addHelper( 'mail', new \Aimeos\MW\View\Helper\Mail\Standard( $view, $this->emailMock ) );

		$this->object->setView( $view );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
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

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'One or more products', $output );
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
}
