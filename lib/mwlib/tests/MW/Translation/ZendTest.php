<?php

/**
 * Test class for MW_Translation_ZendTest.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Translation_ZendTest extends MW_Unittest_Testcase
{
	/**
	 * @var MW_Translation_Zend
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
		$suite  = new PHPUnit_Framework_TestSuite('MW_Translation_Zend');
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

		$this->_translationSources = array(
			'testDomain' => dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case1',
			'otherTestDomain' => dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case2', // no file!
			'thirdTestDomain' => dirname(__FILE__) . $ds . 'testfiles' . $ds . 'case3',
		);

		$this->_object = new MW_Translation_Zend( $this->_translationSources, 'csv', 'ru_ZD' );
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
		$this->assertEquals( 'Test default return', $this->_object->dt( 'otherTestDomain', 'Test default return' ) );

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
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 0 ) );
		$this->assertEquals( 'singular translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 1 ) );
		$this->assertEquals( 'plural 1 translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 2 ) );
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 5 ) );

		$this->assertEquals( 'plural 1 translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 22 ) );
		$this->assertEquals( 'plural 2 translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 25 ) );
		$this->assertEquals( 'singular translation', $this->_object->dn( 'otherTestDomain', 'File', 'Files', 31 ) );

		$this->assertEquals( 'tests', $this->_object->dn( 'invalidTestDomain', 'test', 'tests', 2 ) );
	}

	// test using the testfiles/case1/ka_GE file; lang: german
	public function testAdapterGettext()
	{
		$object = new MW_Translation_Zend( $this->_translationSources, 'gettext', 'ka_GE', array('disableNotices'=>true) );

		$this->assertEquals( 'Aktualisierung', $object->dt( 'testDomain', 'Update' ) );

		$this->assertEquals( 'Auto', $object->dn( 'testDomain', 'Car', 'Cars', 0 ) );
		$this->assertEquals( 'Datei', $object->dn( 'testDomain', 'File', 'Files', 1 ) );
	}

}
