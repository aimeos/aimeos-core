<?php

namespace Aimeos\Perf;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class CatalogTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $root;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext( 'unitperf' );

		// parser warm up so files are already parsed (same as APC is used)

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$this->root = $catalogManager->getTree( null, array( 'text', 'media' ), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
	}


	public function testTree()
	{
		$start = microtime( true );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$catalogManager->getTree( null, array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree w/o ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testTreeWithId()
	{
		$start = microtime( true );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$catalogManager->getTree( $this->root->getId(), array( 'text', 'media' ) );

		$stop = microtime( true );
		echo "\n    catalog tree with ID: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
