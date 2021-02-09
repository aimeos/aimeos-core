<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Translation;


class SerializedArrayTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'testDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case1' ),
			'otherTestDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case2' ), // no file for ru_XX!
			'thirdtestDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case3' ),
		);

		$this->object = new \Aimeos\MW\Translation\SerializedArray( $translationSources, 'ru_XX' );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testDt()
	{
		$this->assertEquals( 'singular translation', $this->object->dt( 'testDomain', 'File' ) );
		$this->assertEquals( 'singular translation file', $this->object->dt( 'thirdtestDomain', 'File' ) );
		$this->assertEquals( 'Car', $this->object->dt( 'thirdtestDomain', 'Car' ) );
		$this->assertEquals( 'test', $this->object->dt( 'invalidTestDomain', 'test' ) );
	}


	public function testDn()
	{
		/*
		 * plural for RU: 3 pl forms
		 * 0, if $n == 1, 21, 31, 41, ...
		 * 1, if $n == 2..4, 22..24, 32..34, ...
		 * 2, if $n == 5..20, 25..30, 35..40, .
		 */
		$this->assertEquals( 'plural 2 translation', $this->object->dn( 'testDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular translation', $this->object->dn( 'testDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'plural 1 translation', $this->object->dn( 'testDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'plural 2 translation', $this->object->dn( 'testDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'plural 1 translation', $this->object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'plural 2 translation', $this->object->dn( 'testDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'singular translation', $this->object->dn( 'testDomain', 'File', 'Files', 31 ) );

		$this->assertEquals( 'singular translation', $this->object->dn( 'testDomain', 'File', 'Files', -1 ) );
		$this->assertEquals( 'plural 1 translation', $this->object->dn( 'testDomain', 'File', 'Files', -22 ) );

		$this->assertEquals( 'Files', $this->object->dn( 'thirdtestDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular translation file', $this->object->dn( 'thirdtestDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Cars', $this->object->dn( 'thirdtestDomain', 'Car', 'Cars', 0 ) );
		$this->assertEquals( 'Car', $this->object->dn( 'thirdtestDomain', 'Car', 'Cars', 1 ) );
		$this->assertEquals( 'tests', $this->object->dn( 'invalidTestDomain', 'test', 'tests', 2 ) );
	}


	public function testAbstractGetTranslationFileFallback()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'testDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case1' ) );
		$object = new \Aimeos\MW\Translation\SerializedArray( $srcs, 'de_DE' );

		$this->assertEquals( 'plural 1 translation', $object->dn( 'testDomain', 'File', 'Files', 5 ) );
		$this->assertEquals( 'plural 1 translation', $object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'singular translation', $object->dn( 'testDomain', 'File', 'Files', -1 ) );
	}


	public function testAbstractGetTranslationFileFallbackNoFile()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'otherTestDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case2' ) );
		$object = new \Aimeos\MW\Translation\SerializedArray( $srcs, 'de' );

		$this->assertEquals( 'Test default return', $object->dt( 'otherTestDomain', 'Test default return' ) );
	}


	public function testAbstractGetTranslationFileFallbackInvalidLocale()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'otherTestDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case2' ) );
		$object = new \Aimeos\MW\Translation\SerializedArray( $srcs, 'xx_XX' );

		$this->assertEquals( 'Test default return', $object->dt( 'otherTestDomain', 'Test default return' ) );
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

		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'testDomain' => array( __DIR__ . $ds . 'testfiles' . $ds . 'case1' ) );

		foreach( $lcList as $index => $lcs )
		{
			foreach( $lcs as $lc )
			{
				$object = new \Aimeos\MW\Translation\SerializedArray( $srcs, $lc );
				$this->assertEquals( 'test', $object->dn( 'testDomain', 'test', 'tests', $index ) );
			}
		}
	}


	public function testGetAll()
	{
		$result = $this->object->all( 'testDomain' );

		$this->assertArrayHasKey( 'File', $result );
		$this->assertEquals( 'singular translation', $result['File'][0] );
		$this->assertEquals( 'plural 1 translation', $result['File'][1] );
		$this->assertEquals( 'plural 2 translation', $result['File'][2] );
	}

}
