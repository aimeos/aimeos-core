<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Customer_Email_Watch_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_arcavias;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Customer_Email_Watch_Default( $this->_context, $this->_arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product notification e-mails', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends e-mails for watched products';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
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

		$this->_context->setMail( $mailStub );


		$product = $this->_getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( 'Controller_Jobs_Customer_Email_Watch_Default' )
			->setConstructorArgs( array( $this->_context, $this->_arcavias ) )
			->setMethods( array( '_getListProducts' ) )
			->getMock();

		$object->expects( $this->once() )->method( '_getListProducts' )
			->will( $this->returnValue( array( -1 => array( 'item' => $product, 'price' => reset( $prices ) ) ) ) );


		$object->run();
	}


	public function testRunException()
	{
		$mailStub = $this->getMockBuilder( 'MW_Mail_None' )
			->disableOriginalConstructor()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->throwException( new Exception() ) );

		$this->_context->setMail( $mailStub );


		$product = $this->_getProductItem();
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		$object = $this->getMockBuilder( 'Controller_Jobs_Customer_Email_Watch_Default' )
			->setConstructorArgs( array( $this->_context, $this->_arcavias ) )
			->setMethods( array( '_getListProducts' ) )
			->getMock();

		$object->expects( $this->once() )->method( '_getListProducts' )
			->will( $this->returnValue( array( -1 => array( 'item' => $product, 'price' => reset( $prices ) ) ) ) );


		$object->run();
	}


	protected function _getProductItem()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );
		$items = $manager->searchItems( $search, array( 'media', 'price', 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
