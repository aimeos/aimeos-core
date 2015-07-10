<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MW_View_Helper_Price_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Translate_Default( $view, new MW_Translation_None( 'en' ) );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, ',', ' ' );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		$this->_object = new MW_View_Helper_Price_Default( $view );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testTransformSingle()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'price' );

		$price = $manager->createItem();
		$price->setValue( '10.00' );
		$price->setCosts( '1.00' );
		$price->setRebate( '2.00' );
		$price->setTaxrate( '20.00' );


		$output = $this->_object->transform( $price );
		$this->assertRegexp( '/.*1.*10,00.*2,00.*1,00.*20,00.*/smU', $output );
	}


	public function testTransformMultiple()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'price' );

		$price = $manager->createItem();
		$price->setValue( '10.00' );
		$price->setCosts( '1.00' );
		$price->setRebate( '2.00' );
		$price->setTaxrate( '20.00' );

		$price2 = $manager->createItem();
		$price2->setQuantity( 5 );
		$price2->setValue( '20.00' );
		$price2->setCosts( '2.00' );
		$price2->setRebate( '4.00' );
		$price2->setTaxrate( '10.00' );

		$output = $this->_object->transform( array( $price, $price2 ) );
		$this->assertRegexp( '/.*1.*10,00.*2,00.*1,00.*20,00.*5.*20,00.*4,00.*2,00.*10,00.*/smU', $output );
	}


	public function testTransformInvalid()
	{
		$this->setExpectedException( 'MW_View_Exception' );
		$this->_object->transform( new stdClass() );
	}
}
