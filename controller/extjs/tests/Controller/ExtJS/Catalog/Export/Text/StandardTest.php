<?php

namespace Aimeos\Controller\ExtJS\Catalog\Export\Text;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
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
		$this->object = new \Aimeos\Controller\ExtJS\Catalog\Export\Text\Standard( $this->context );
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


	public function testExportCSVFile()
	{
		$manager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$node = $manager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.label', array( 'Root', 'Tee' ) ) );

		$ids = array();
		foreach( $manager->searchItems( $search ) as $item ) {
			$ids[$item->getLabel()] = $item->getId();
		}

		$params = new \stdClass();
		$params->lang = array( 'de', 'fr' );
		$params->items = array( $node->getId() );
		$params->site = $this->context->getLocale()->getSite()->getCode();

		$result = $this->object->exportFile( $params );

		$this->assertTrue( array_key_exists( 'file', $result ) );

		$file = substr( $result['file'], 9, -14 );
		$this->assertTrue( file_exists( $file ) );

		$zip = new \ZipArchive();
		$zip->open( $file );

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
		if( !is_dir( $testdir ) && mkdir( $testdir, 0755, true ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
		}

		$zip->extractTo( $testdir );
		$zip->close();

		if( unlink( $file ) === false ) {
			throw new \Exception( 'Unable to remove export file' );
		}

		$lines = $langs = array();
		$langs['fr'] = $testdir . DIRECTORY_SEPARATOR . 'fr.csv';
		$langs['de'] = $testdir . DIRECTORY_SEPARATOR . 'de.csv';

		foreach( $langs as $lang => $path )
		{
			$this->assertTrue( file_exists( $path ) );
			$fh = fopen( $path, 'r' );
			while( ( $data = fgetcsv( $fh ) ) != false ) {
				$lines[$lang][] = $data;
			}

			fclose( $fh );
			if( unlink( $path ) === false ) {
				throw new \Exception( 'Unable to remove export file' );
			}
		}

		if( rmdir( $testdir ) === false ) {
			throw new \Exception( 'Unable to remove test export directory' );
		}

		$this->assertEquals( 'Language ID', $lines['de'][0][0] );
		$this->assertEquals( 'Text', $lines['de'][0][6] );

		$this->assertEquals( 'de', $lines['de'][3][0] );
		$this->assertEquals( 'Root', $lines['de'][3][1] );
		$this->assertEquals( $ids['Root'], $lines['de'][3][2] );
		$this->assertEquals( 'default', $lines['de'][3][3] );
		$this->assertEquals( 'name', $lines['de'][3][4] );
		$this->assertEquals( '', $lines['de'][3][6] );

		$this->assertEquals( 'de', $lines['de'][23][0] );
		$this->assertEquals( 'Tee', $lines['de'][23][1] );
		$this->assertEquals( $ids['Tee'], $lines['de'][23][2] );
		$this->assertEquals( 'unittype8', $lines['de'][23][3] );
		$this->assertEquals( 'long', $lines['de'][23][4] );
		$this->assertEquals( 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.', $lines['de'][23][6] );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->object->getServiceDescription();
		$expected = array(
			'Catalog_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "array", "name" => "lang", "optional" => true ),
				),
				"returns" => "",
			),
		);

		$this->assertEquals( $expected, $actual );
	}
}