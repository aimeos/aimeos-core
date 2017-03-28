<?php

namespace Aimeos\MW\Translation;


/**
 * Test class for \Aimeos\MW\Translation\NoneTest.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class NoneTest extends \PHPUnit_Framework_TestCase
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
		$this->object = new \Aimeos\MW\Translation\None( 'ru_XX' );
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


	public function testConstructTwoLetterLocale()
	{
		new \Aimeos\MW\Translation\None( 'de' );
	}


	public function testConstructFiveLetterLocale()
	{
		new \Aimeos\MW\Translation\None( 'de_DE' );
	}


	public function testConstructInvalidUnderscoreLocale()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Translation\\Exception' );
		new \Aimeos\MW\Translation\None( 'de_' );
	}


	public function testConstructInvalidCaseLocale()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Translation\\Exception' );
		new \Aimeos\MW\Translation\None( 'de_de' );
	}


	public function testConstructInvalidCharLocale()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Translation\\Exception' );
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
				'am', 'ar','bh', 'fil', 'fr', 'gun', 'hi', 'ln', 'lv','mg', 'nso', 'xbr', 'ti', 'wa', 'pt_BR'
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
		$this->assertEquals( [], $this->object->getAll( 'testDomain' ) );
	}

}
