<?php

namespace Aimeos\Controller\Jobs\Customer\Email\Watch;


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
		$this->context = \TestHelper::getContext();
		$this->aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Customer\Email\Watch\Standard( $this->context, $this->aimeos );
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
		$this->assertEquals( 'Product notification e-mails', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends e-mails for watched products';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
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


		$product = $this->getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( array( $this->context, $this->aimeos ) )
			->setMethods( array( 'getListProducts' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getListProducts' )
			->will( $this->returnValue( array( -1 => array( 'item' => $product, 'price' => reset( $prices ) ) ) ) );


		$object->run();
	}


	public function testRunException()
	{
		$mailStub = $this->getMockBuilder( '\\Aimeos\\MW\\Mail\\None' )
			->disableOriginalConstructor()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->throwException( new \Exception() ) );

		$this->context->setMail( $mailStub );


		$product = $this->getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( '\\Aimeos\\Controller\\Jobs\\Customer\\Email\\Watch\\Standard' )
			->setConstructorArgs( array( $this->context, $this->aimeos ) )
			->setMethods( array( 'getListProducts' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getListProducts' )
			->will( $this->returnValue( array( -1 => array( 'item' => $product, 'price' => reset( $prices ) ) ) ) );


		$object->run();
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );
		$items = $manager->searchItems( $search, array( 'media', 'price', 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
