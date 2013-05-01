<?php

/**
 * Test class for MW_Translation_NoneTest.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Translation_NoneTest extends MW_Unittest_Testcase
{
	/**
	 * @var MW_Translation_None
	 */
	private $_object;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_Translation_None');
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
		$this->_object = new MW_Translation_None( 'ru_XX' );
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
		$this->assertEquals( 'File', $this->_object->dt( 'testDomain', 'File' ) );
		$this->assertEquals( 'ελληνική γλώσσα', $this->_object->dt( 'testDomain', 'ελληνική γλώσσα' ) );
	}


	public function testDn()
	{
		/*
		 * plural for RU: 3 pl forms
		 * 0, if $n == 1, 21, 31, 41, ...
		 * 1, if $n == 2..4, 22..24, 32..34, ...
		 * 2, if $n == 5..20, 25..30, 35..40, .
		 */
		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'File', $this->_object->dn( 'testDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'File', $this->_object->dn( 'testDomain', 'File', 'Files', 31 ) );

		$this->assertEquals( 'File', $this->_object->dn( 'testDomain', 'File', 'Files', -1 ) );
		$this->assertEquals( 'Files', $this->_object->dn( 'testDomain', 'File', 'Files', -22 ) );


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

		foreach( $lcList as $index => $lcs )
		{
			foreach( $lcs as $lc )
			{
				$object = new MW_Translation_None( $lc );
				$this->assertEquals( 'test', $object->dn( 'testDomain', 'test', 'tests', $index ) );
			}
		}
	}

}
