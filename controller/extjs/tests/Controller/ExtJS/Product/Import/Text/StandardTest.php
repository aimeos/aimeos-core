<?php

namespace Aimeos\Controller\ExtJS\Product\Import\Text;


/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $testdir;
	private $testfile;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelper::getContext();

		$this->testdir = $this->context->getConfig()->get( 'controller/extjs/product/import/text/standard/uploaddir', './tmp' );
		$this->testfile = $this->testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->testdir ) && mkdir( $this->testdir, 0775, true ) === false ) {
			throw new \Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->testdir ) );
		}

		$this->object = new \Aimeos\Controller\ExtJS\Product\Import\Text\Standard( $this->context );
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


	public function testGetServiceDescription()
	{
		$desc = $this->object->getServiceDescription();
		$this->assertInternalType( 'array', $desc );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.importFile'] ) );
	}


	public function testImportFromCSVFile()
	{
		$data = array();
		$data[] = '"Language ID","Type","Code","List type","Text type","Text ID","Text"' . "\n";
		$data[] = '"en","product","ABCD","default","long","","ABCD: long"' . "\n";
		$data[] = '"en","product","ABCD","default","metadescription","","ABCD: meta desc"' . "\n";
		$data[] = '"en","product","ABCD","default","metakeywords","","ABCD: meta keywords"' . "\n";
		$data[] = '"en","product","ABCD","default","metatitle","","ABCD: meta title"' . "\n";
		$data[] = '"en","product","ABCD","default","name","","ABCD: name"' . "\n";
		$data[] = '"en","product","ABCD","default","short","","ABCD: short"' . "\n";
		$data[] = ' ';

		$ds = DIRECTORY_SEPARATOR;
		$csv = 'en-product-test.csv';
		$filename = PATH_TESTS . $ds . 'tmp' . $ds . 'product-import.zip';

		if( file_put_contents( PATH_TESTS . $ds . 'tmp' . $ds . $csv, implode( '', $data ) ) === false ) {
			throw new \Exception( sprintf( 'Unable to write test file "%1$s"', $csv ) );
		}

		$zip = new \ZipArchive();
		$zip->open( $filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );
		$zip->addFile( PATH_TESTS . $ds . 'tmp' . $ds . $csv, $csv );
		$zip->close();

		if( unlink( PATH_TESTS . $ds . 'tmp' . $ds . $csv ) === false ) {
			throw new \Exception( 'Unable to remove export file' );
		}


		$params = new \stdClass();
		$params->site = $this->context->getLocale()->getSite()->getCode();
		$params->items = $filename;

		$this->object->importFile( $params );

		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'product' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'ABCD:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = array();
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $productManager->getSubManager( 'lists' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.lists.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'product.lists.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}


		foreach( $textItems as $item ) {
			$this->assertEquals( 'ABCD:', substr( $item->getContent(), 0, 5 ) );
		}

		$this->assertEquals( 6, count( $textItems ) );
		$this->assertEquals( 6, count( $listItems ) );

		if( file_exists( $filename ) !== false ) {
			throw new \Exception( 'Import file was not removed' );
		}
	}


	public function testUploadFile()
	{
		$jobController = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context );

		$testfiledir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->testdir ) ) );


		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->testfile,
			'error' => UPLOAD_ERR_OK,
		);

		$params = new \stdClass();
		$params->items = $this->testfile;
		$params->site = $this->context->getLocale()->getSite()->getCode();

		$result = $this->object->uploadFile( $params );

		$this->assertTrue( file_exists( $result['items'] ) );
		unlink( $result['items'] );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'job.label' => 'file.txt' ) ) ) ),
		);

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );

		$deleteParams = (object) array(
			'site' => 'unittest',
			'items' => $result['items'][0]->{'job.id'},
		);

		$jobController->deleteItems( $deleteParams );

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testUploadFileExceptionNoUploadFile()
	{
		$_FILES = array();

		$params = new \stdClass();
		$params->items = 'file.txt';
		$params->site = $this->context->getLocale()->getSite()->getCode();

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->uploadFile( $params );
	}
}