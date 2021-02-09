<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Translation;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Translation\None( 'ru_XX' );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testConstructTwoLetterLocale()
	{
		$this->assertInstanceOf( \Aimeos\MW\Translation\Iface::class, new \Aimeos\MW\Translation\None( 'de' ) );
	}


	public function testConstructFiveLetterLocale()
	{
		$this->assertInstanceOf( \Aimeos\MW\Translation\Iface::class, new \Aimeos\MW\Translation\None( 'de_DE' ) );
	}


	public function testConstructInvalidUnderscoreLocale()
	{
		$this->expectException( \Aimeos\MW\Translation\Exception::class );
		new \Aimeos\MW\Translation\None( 'de_' );
	}


	public function testConstructInvalidCaseLocale()
	{
		$this->expectException( \Aimeos\MW\Translation\Exception::class );
		new \Aimeos\MW\Translation\None( 'de_de' );
	}


	public function testConstructInvalidCharLocale()
	{
		$this->expectException( \Aimeos\MW\Translation\Exception::class );
		new \Aimeos\MW\Translation\None( 'd' );
	}


	public function testDt()
	{
		$this->assertEquals( 'File', $this->object->dt( 'testDomain', 'File' ) );
		$this->assertEquals( 'ελληνική γλώσσα', $this->object->dt( 'testDomain', 'ελληνική γλώσσα' ) );
	}


	public function testDn()
	{
		/*
		 * plural for RU: 3 pl forms
		 * 0, if $n == 1, 21, 31, 41, ...
		 * 1, if $n == 2..4, 22..24, 32..34, ...
		 * 2, if $n == 5..20, 25..30, 35..40, .
		 */
		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'File', $this->object->dn( 'testDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'File', $this->object->dn( 'testDomain', 'File', 'Files', 31 ) );

		$this->assertEquals( 'File', $this->object->dn( 'testDomain', 'File', 'Files', -1 ) );
		$this->assertEquals( 'Files', $this->object->dn( 'testDomain', 'File', 'Files', -22 ) );
	}


	public function testAbstractGetPluralIndexAll()
	{
		// test first match; plural = singular for langugages with no or complex plural versions

		$lcList = array(
			0 => array(
				'am', 'ar', 'bh', 'fil', 'fr', 'gun', 'hi', 'ln', 'lv', 'mg', 'nso', 'xbr', 'ti', 'wa', 'pt_BR'
			),
			1 => array(
				'af', 'az', 'bn', 'bg', 'ca', 'da', 'de', 'el', 'en', 'eo', 'es',
				'et', 'eu', 'fa', 'fi', 'fo', 'fur', 'fy', 'gl', 'gu', 'ha', 'he',
				'hu', 'is', 'it', 'ku', 'lb', 'ml', 'mn', 'mr', 'nah', 'nb', 'ne',
				'nl', 'nn', 'no', 'om', 'or', 'pa', 'pap', 'ps', 'pt', 'so', 'sq',
				'sv', 'sw', 'ta', 'te', 'tk', 'ur', 'zu',
				'be', 'bs', 'hr', 'ru', 'sr', 'uk', 'cs', 'sk',
				'cy', 'ga', 'mt', 'pl', 'ro', 'lt', 'mk', 'sl'
			),
		);

		foreach( $lcList as $index => $lcs )
		{
			foreach( $lcs as $lc )
			{
				$object = new \Aimeos\MW\Translation\None( $lc );
				$this->assertEquals( 'test', $object->dn( 'testDomain', 'test', 'tests', $index ) );
			}
		}
	}


	public function testGetAll()
	{
		$this->assertEquals( [], $this->object->all( 'testDomain' ) );
	}

}
