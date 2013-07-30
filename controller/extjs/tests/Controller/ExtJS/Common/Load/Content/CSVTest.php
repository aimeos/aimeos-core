<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Common_Load_Content_CSVTest extends MW_Unittest_Testcase
{
	private $_content;
	private $_testfile;
	private $_testdir;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Common_Load_Content_CSVTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_testdir = 'tmp' . DIRECTORY_SEPARATOR . 'content';
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'en.csv';

		if( mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions 0775', $this->_testdir ) );
		}

		$this->_content = new Controller_ExtJS_Common_Load_Content_CSV( $this->_testfile, 'en' );

		for( $i = 0; $i < 10; $i++ ) {
			$this->_content->addRow( array( 'Row nr '.$i, 'test', '3 column', '4 column', 'end test' ) );
		}
	}

	protected function tearDown()
	{
		unlink( $this->_testfile );
		rmdir( $this->_testdir );
		$this->_content = null;
	}

	public function testAddRow()
	{
		for( $i = 10; $i < 20; $i++ ) {
			$this->_content->addRow( array( 'Row nrs '.$i, 'tests', '3 columns', '4 columns', 'end tests' ) );
		}

		$i = 0;
		foreach( $this->_content as $row )
		{
			if( $i > 9) {
				$this->assertEquals(  array( 'Row nrs '.$i, 'tests', '3 columns', '4 columns', 'end tests' ), $row );
			}
			$i++;
		}
	}

	public function testGetResource()
	{
		$this->assertEquals( $this->_testfile, $this->_content->getResource() );
	}


	public function testRewind()
	{
		foreach( $this->_content as $row ) {}
		$lastKey = $this->_content->key();

		$this->_content->rewind();

		$this->assertEquals( 0, $this->_content->key() );
		$this->assertEquals( 9, $lastKey );
	}

	public function testNext()
	{
		$this->_content->rewind();
		$this->_content->next();

		$this->assertEquals( 1, $this->_content->key() );
	}

	public function testCurrent()
	{
		$this->_content->rewind();

		$this->assertEquals( array( 'Row nr 0', 'test', '3 column', '4 column', 'end test' ), $this->_content->current() );
	}

	public function testValid()
	{
		$this->_content->rewind();
		$first = $this->_content->valid();

		foreach( $this->_content as $row ){}

		$this->assertTrue( $first );
		$this->assertFalse( $this->_content->valid() );
	}
}
