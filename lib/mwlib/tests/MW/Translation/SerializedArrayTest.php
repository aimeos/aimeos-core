<?php

/**
 * Test class for MW_Translation_SerialisedArrayTest.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Translation_SerializedArrayTest extends MW_Unittest_Testcase
{
	/**
	 * @var MW_Translation_SerialisedArray
	 */
	protected $_object;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_Translation_SerializedArray');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'testDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case1' ),
			'otherTestDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case2' ), // no file for ru_XX!
			'thirdtestDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case3' ),
		);

		$this->_object = new MW_Translation_SerializedArray( $translationSources, 'ru_XX' );
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


	public function testDt()
	{
		$this->assertEquals( 'singular translation', $this->_object->dt( 'testDomain', 'File' ) );
		$this->assertEquals( 'singular translation file', $this->_object->dt( 'thirdtestDomain', 'File' ) );
		$this->assertEquals( 'Car', $this->_object->dt( 'thirdtestDomain', 'Car' ) );
		$this->assertEquals( 'test', $this->_object->dt( 'invalidTestDomain', 'test' ) );
	}


	public function testDn()
	{
		/*
		 * plural for RU: 3 pl forms
		 * 0, if $n == 1, 21, 31, 41, ...
		 * 1, if $n == 2..4, 22..24, 32..34, ...
		 * 2, if $n == 5..20, 25..30, 35..40, .
		 */
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'testDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular translation', $this->_object->dn( 'testDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'plural 1 translation', $this->_object->dn( 'testDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'testDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'plural 1 translation', $this->_object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'testDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'singular translation', $this->_object->dn( 'testDomain', 'File', 'Files', 31 ) );

		$this->assertEquals( 'singular translation', $this->_object->dn( 'testDomain', 'File', 'Files', -1 ) );
		$this->assertEquals( 'plural 1 translation', $this->_object->dn( 'testDomain', 'File', 'Files', -22 ) );

		$this->assertEquals( 'Files', $this->_object->dn( 'thirdtestDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular translation file', $this->_object->dn( 'thirdtestDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Cars', $this->_object->dn( 'thirdtestDomain', 'Car', 'Cars', 0 ) );
		$this->assertEquals( 'Car', $this->_object->dn( 'thirdtestDomain', 'Car', 'Cars', 1 ) );
		$this->assertEquals( 'tests', $this->_object->dn( 'invalidTestDomain', 'test', 'tests', 2 ) );
	}


	public function testAbstractGetTranslationFileFallback()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'testDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case1' ) );
		$object = new MW_Translation_SerializedArray( $srcs, 'de_DE' );

		$this->assertEquals( 'plural 1 translation', $object->dn( 'testDomain', 'File', 'Files', 5 ) );
		$this->assertEquals( 'plural 1 translation', $object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'singular translation', $object->dn( 'testDomain', 'File', 'Files', -1 ) );
	}


	public function testAbstractGetTranslationFileFallbackNoFile()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'otherTestDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case2' ) );
		$object = new MW_Translation_SerializedArray( $srcs, 'de' );

		$this->assertEquals( 'Test default return', $object->dt( 'otherTestDomain', 'Test default return' ) );
	}


	public function testAbstractGetTranslationFileFallbackInvalidLocale()
	{
		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'otherTestDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case2' ) );
		$object = new MW_Translation_SerializedArray( $srcs, 'xx_XX' );

		$this->assertEquals( 'Test default return', $object->dt( 'otherTestDomain', 'Test default return' ) );
	}


	public function testAbstractGetPluralIndexAll()
	{
		// test first match; plural = singular for langugages with no or complex plural versions

		$lcList = array(
			0 => array(
				'1', // test input = output
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

		$ds = DIRECTORY_SEPARATOR;
		$srcs = array( 'testDomain' => array( dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case1' ) );

		foreach( $lcList as $index => $lcs )
		{
			foreach( $lcs as $lc )
			{
				$object = new MW_Translation_SerializedArray( $srcs, $lc );
				$this->assertEquals( 'test', $object->dn( 'testDomain', 'test', 'tests', $index ) );
			}
		}
	}

}
