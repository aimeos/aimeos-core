<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Perf_CatalogTest extends PHPUnit_Framework_TestCase
{
	private $_context;
	private $_root;


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
		$catalogManager->getTree( null, array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree w/o ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testTreeWithId()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$catalogManager->getTree( $this->_root->getId(), array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree with ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
