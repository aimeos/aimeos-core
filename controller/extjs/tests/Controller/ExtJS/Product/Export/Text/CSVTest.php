<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_Export_Text_CSVTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_Export_Text_CSVTest' );
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
		$this->_object = new Controller_ExtJS_Product_Export_Text_CSV( TestHelper::getContext() );
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


	public function testExportFile()
	{
		$context = TestHelper::getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$criteria = $productManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.code', 'CNE' );
		$criteria->setConditions( $criteria->compare( '==', 'product.code', 'CNE' ) );

		$searchResult = $productManager->searchItems( $criteria );

		if ( ( $productItem = reset( $searchResult ) ) === false ) {
			throw new Exception( 'No item with product code CNE found' );
		}

		$params = new stdClass();
		$params->site = $context->getLocale()->getSite()->getCode();
		$params->items = $productItem->getId();
		$params->lang = array( 'de', 'en' );

		$result = $this->_object->exportFile( $params );
		$file = $result['filename'];

		$this->assertTrue( file_exists( $file ) );

		$zip = new ZipArchive();
		$zip->open($file);

		if( mkdir( 'tmp' . DIRECTORY_SEPARATOR . 'csvexport' ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
		}

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
		$zip->extractTo( $testdir );
		$zip->close();

		if( unlink( $file ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$langs['en'] = $testdir . DIRECTORY_SEPARATOR . 'en.csv';
		$langs['de'] = $testdir . DIRECTORY_SEPARATOR . 'de.csv';

		foreach( $langs as $lang )
		{
			$this->assertTrue( file_exists( $lang ) );
			$fh = fopen( $lang, 'r' );
			$lines[ $lang ] = fgetcsv( $fh );
			fclose( $fh );
			if( unlink( $lang ) === false ) {
				throw new Exception( 'Unable to remove export file' );
			}
		}

		if( rmdir( $testdir ) === false ) {
			throw new Exception( 'Unable to remove test export directory' );
		}

		foreach( $lines as $lang => $line )
		{
			$this->assertEquals( $line[0], 'Language ID' );
			$this->assertEquals( $line[1], 'Product type' );
			$this->assertEquals( $line[2], 'Product code' );
			$this->assertEquals( $line[3], 'List type' );
			$this->assertEquals( $line[4], 'Text type' );
			$this->assertEquals( $line[5], 'Text ID' );
			$this->assertEquals( $line[6], 'Text' );
		}
	}
}