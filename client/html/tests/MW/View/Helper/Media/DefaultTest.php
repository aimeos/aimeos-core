<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MW_View_Helper_Media_DefaultTest extends MW_Unittest_Testcase
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

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		$this->_object = new MW_View_Helper_Media_Default( $view );
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


	public function testTransformImage()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'media' );

		$item = $manager->createItem();
		$item->setPreview( 'path/to/preview.jpg' );
		$item->setUrl( 'path/to/original.jpg' );
		$item->setMimetype( 'image/jpeg' );
		$item->setLabel( 'test image' );

		$attr = array( 'class' => 'testclass' );
		$expected = '<div class="testclass" ><img src="/base/path/to/preview.jpg" title="test image"  /></div>';

		$output = $this->_object->transform( $item, '/base', $attr );
		$this->assertContains( $expected, $output );
	}


	public function testTransformMisc()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'media' );

		$item = $manager->createItem();
		$item->setPreview( 'path/to/preview.jpg' );
		$item->setUrl( 'path/to/file.pdf' );
		$item->setMimetype( 'application/pdf' );
		$item->setLabel( 'test PDF' );

		$boxattr = array( 'class' => 'boxclass' );
		$itemattr = array( 'class' => 'itemclass' );
		$expected = '<a href="/base/path/to/file.pdf" class="boxclass" ><img src="/base/path/to/preview.jpg" title="test PDF" class="itemclass"  />test PDF</a>';

		$output = $this->_object->transform( $item, '/base', $boxattr, $itemattr );
		$this->assertContains( $expected, $output );
	}
}
