<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */

class Perf_CatalogTest extends MW_Unittest_Testcase
{
	private $_context;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Perf_CatalogTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$this->_context = TestHelper::getContext( 'unitperf' );

		// parser warm up so files are already parsed (same as APC is used)

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$this->_root = $catalogManager->getTree( null, array( 'text', 'media' ), MW_Tree_Manager_Abstract::LEVEL_ONE );
	}


	public function testTree()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$result = $catalogManager->getTree( null, array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree w/o ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testTreeWithId()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$result = $catalogManager->getTree( $this->_root->getId(), array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree with ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
