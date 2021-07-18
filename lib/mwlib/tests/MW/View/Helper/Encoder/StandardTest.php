<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Encoder;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Encoder\Standard( $view );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Iface::class, $this->object->transform() );
	}


	public function testTransformAttrTrusted()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '<a href=" ">', $enc->attr( '<a href="
">', $enc::TRUST, ' ' ) );
	}


	public function testTransformAttrValid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( 'an attribute', $enc->attr( 'an attribute' ) );
	}


	public function testTransformAttrInvalid()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '<a>&quot;attri&#96;bute&apos;</a>', $enc->attr( '<a>"attri`bute\'</a>' ) );
	}


	public function testTransformAttrArray()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '[&quot;\\u0026quot;&quot;]', $enc->attr( ['&quot;'] ) );
	}


	public function testTransformAttrObject()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '{&quot;key&quot;:&quot;\\u0026quot;&quot;}', $enc->attr( (object) ['key' => '&quot;'] ) );
	}


	public function testTransformAttrNewline()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '{&quot;key&quot;:&quot;\\\\n&quot;}', $enc->attr( (object) ['key' => '\n'] ) );
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


	public function testTransformHtmlArray()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '[&quot;\u0026quot;&quot;]', $enc->html( ['&quot;'] ) );
	}


	public function testTransformHtmlObject()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '&quot;key&quot;:&quot;\u0026quot;&quot;', $enc->html( (object) ['key' => '&quot;'] ) );
	}


	public function testTransformJsApostroph()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '&apos;', $enc->js( '\'' ) );
	}


	public function testTransformJsBacktick()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '\&#96;', $enc->js( '`' ) );
	}


	public function testTransformJsQuote()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '&quot;', $enc->js( '"' ) );
	}


	public function testTransformJsNewline()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '\\n', $enc->js( '\n' ) );
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


	public function testTransformXmlArray()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '["\u0026quot;"]', $enc->xml( ['&quot;'] ) );
	}


	public function testTransformXmlObject()
	{
		$enc = $this->object->transform();

		$this->assertEquals( '{"key":"\u0026quot;"}', $enc->xml( (object) ['key' => '&quot;'] ) );
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
