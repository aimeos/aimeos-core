<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_Encoder_Default.
 */
class MW_View_Helper_Encoder_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new MW_View_Helper_Encoder_Default( $view );
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


	public function testTransform()
	{
		$this->assertInstanceOf( 'MW_View_Helper_Interface', $this->_object->transform() );
	}


	public function testTransformAttrTrusted()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( 'an attribute', $enc->attr( 'an attribute', $enc::TRUST ) );
	}


	public function testTransformAttrValid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( 'an attribute', $enc->attr( 'an attribute' ) );
	}


	public function testTransformAttrInvalid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( '&lt;a&gt;&quot;attribute&#039;&lt;/a&gt;', $enc->attr( '<a>"attribute\'</a>' ) );
	}


	public function testTransformHtmlTrusted()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( '<a>link</a>', $enc->html( '<a>link</a>', $enc::TRUST ) );
	}


	public function testTransformHtmlValid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( 'a text', $enc->html( 'a text' ) );
	}


	public function testTransformHtmlInvalid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( '&lt;a&gt;text&lt;/a&gt;', $enc->html( '<a>text</a>' ) );
	}


	public function testTransformXmlTrusted()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( '<a>link</a>', $enc->xml( '<a>link</a>', $enc::TRUST ) );
	}


	public function testTransformXmlValid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( 'a "text"', $enc->xml( 'a "text"' ) );
	}


	public function testTransformXmlInvalid()
	{
		$enc = $this->_object->transform();

		$this->assertEquals( 'a ]]&gt;&lt;b&gt;text&lt;/b&gt;', $enc->xml( 'a ]]><b>text</b>' ) );
	}
}
