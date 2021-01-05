<?php

namespace Aimeos\MW\Template;


/**
 * Test class for \Aimeos\MW\Session\CMSLite.
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp() : void
	{
		$template = '
<html>
	<head><title>Template test</title></head>
	<body>
<!--###TEMPLATE-->
test template
<!--###LIST-->
<!--###ITEM-->
<div><!--###NUM-->1<!--NUM###--> <!--###TEXT-->example text<!--TEXT###--></div>
<!--ITEM###-->
<!--LIST###-->
<!--TEMPLATE###-->
	</body>
</html>
    	';

		$this->object = new \Aimeos\MW\Template\Base( $template, '<!--###$-->', '<!--$###-->' );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown() : void
	{
	}


	public function testDisable()
	{
		$this->assertEquals( '
<div> text</div>
',
		$this->object->get( 'ITEM' )->disable( 'NUM' )->substitute( array( 'TEXT' => 'text' ) )->str() );
	}


	public function testEnable()
	{
		$this->assertEquals( '
<div>1 text</div>
',
		$this->object->get( 'ITEM' )->enable( 'NUM' )->substitute( array( 'TEXT' => 'text' ) )->str() );
	}


	public function testGet()
	{
		$template = $this->object->get( 'TEMPLATE' );
		$this->assertInstanceOf( \Aimeos\MW\Template\Iface::class, $template );

		$this->assertEquals( '
test template
<!--###LIST-->
<!--###ITEM-->
<div><!--###NUM-->1<!--NUM###--> <!--###TEXT-->example text<!--TEXT###--></div>
<!--ITEM###-->
<!--LIST###-->
',
		$template->str( false ) );
	}

	public function testGetBeginIsNotDefined()
	{
		$this->expectException( \Aimeos\MW\Template\Exception::class );
		$this->object->get( 'NOTDEFINED' );
	}

	public function testGetEndIsNotDefined()
	{
		$template = '
			<html>
				<head><title>Template test</title></head>
				<body>
			<!--###TEMPLATE-->
			test template
			<!--###LIST-->
			<!--###ITEM-->
			<div><!--###NUM-->1<!--NUM###--> <!--###TEXT-->example text<!--TEXT###--></div>

			<!--LIST###-->
			<!--TEMPLATE###-->
				</body>
			</html>
    	';

		$object = new \Aimeos\MW\Template\Base( $template, '<!--###$-->', '<!--$###-->' );

		$this->expectException( \Aimeos\MW\Template\Exception::class );
		$object->get( 'ITEM' );
	}



	public function testGetMarkerNames()
	{
		$this->assertEquals( array( 'TEMPLATE', 'LIST', 'ITEM', 'NUM', 'TEXT' ), $this->object->getMarkerNames() );
	}


	public function testReplace()
	{
		$this->assertEquals( '
<div><!--###NUM-->1<!--NUM###--> <!--###TEXT-->example test<!--TEXT###--></div>
',
		$this->object->get( 'ITEM' )->replace( 'text', 'test' )->str( false ) );
	}


	public function testSubstitute()
	{
		$marker = array(
			'NUM' => '123',
			'TEXT' => 'test'
		);

		$this->assertEquals( '
<div>123 test</div>
',
			$this->object->get( 'ITEM' )->substitute( $marker )->str()
		);
	}

	public function testSubstituteBadTemplate()
	{
		$template = '
			<html>
				<head><title>Template test</title></head>
				<body>
			<!--###TEMPLATE-->
			test template
			<!--###LIST-->
			<!--###ITEM-->
			<div><!--###NUM-->1<!--NUM###--> <!--###TEXT-->example text<!--TEXT###--></div>

			<!--LIST###-->
			<!--TEMPLATE###-->
				</body>
			</html>
    	';

		$object = new \Aimeos\MW\Template\Base( $template, '<!--###$-->', '<!--$###-->' );

		$this->expectException( \Aimeos\MW\Template\Exception::class );
		$object->substitute( array( 'ITEM'=>'Title' ) );
	}

	public function testStr()
	{
		$template = $this->object->get( 'TEMPLATE' );
		$this->assertInstanceOf( \Aimeos\MW\Template\Iface::class, $template );

		$this->assertEquals( '
test template

',
		$template->str() );
	}
}
