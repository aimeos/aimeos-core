<?php

namespace Aimeos\Client\Html\Account\History\Lists;


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
		$this->context = clone \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Account\History\Lists\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
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
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$customer = $this->getCustomerItem( 'UTC001' );
		$this->context->setUserId( $customer->getId() );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="account-history-list">', $output );
		$this->assertRegExp( '#<li class="history-item">#', $output );
		$this->assertRegExp( '#<li class="attr-item order-basic">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-channel">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-payment">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-delivery">.*<span class="value"></span>.*</li>#smuU', $output );
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


	/**
	 * @param string $code
	 */
	protected function getCustomerItem( $code )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
