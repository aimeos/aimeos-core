<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_Encoder_Default.
 */
class MW_View_Helper_Encoder_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();
		$this->object = new MW_View_Helper_Encoder_Default( $view );
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


	public function testTransform()
	{
		$this->assertInstanceOf( 'MW_View_Helper_Interface', $this->object->transform() );
	}


	public function testTransformAttrTrusted()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'an attribute', $enc->attr( 'an attribute', $enc::TRUST ) );
	}


	public function testTransformAttrValid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'an attribute', $enc->attr( 'an attribute' ) );
	}


	public function testTransformAttrInvalid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '&lt;a&gt;&quot;attribute&#039;&lt;/a&gt;', $enc->attr( '<a>"attribute\'</a>' ) );
	}


	public function testTransformHtmlTrusted()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '<a>link</a>', $enc->html( '<a>link</a>', $enc::TRUST ) );
	}


	public function testTransformHtmlValid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'a text', $enc->html( 'a text' ) );
	}


	public function testTransformHtmlInvalid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '&lt;a&gt;text&lt;/a&gt;', $enc->html( '<a>text</a>' ) );
	}


	public function testTransformXmlTrusted()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '<a>link</a>', $enc->xml( '<a>link</a>', $enc::TRUST ) );
	}


	public function testTransformXmlValid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'a "text"', $enc->xml( 'a "text"' ) );
	}


	public function testTransformXmlInvalid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'a ]]&gt;&lt;b&gt;text&lt;/b&gt;', $enc->xml( 'a ]]><b>text</b>' ) );
	}


	public function testTransformUrl()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '__-', $enc->url( ' _-' ) );
	}


	public function testTransformUrlSpecial()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '%5C%27%22%3B%23%2B%7E%2A%24%25%2F%28%29%3D%3F%26', $enc->url( '\\\'";#+~*$%/()=?&' ) );
	}


	public function testTransformUrlHtml()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'test', $enc->url( '<p>test</p>' ) );
	}
}
