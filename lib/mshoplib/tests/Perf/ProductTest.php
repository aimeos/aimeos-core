<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductTest.php 950 2012-07-13 11:58:34Z nsendetzky $
 */

class Perf_ProductTest extends MW_Unittest_Testcase
{
	protected $_context;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Perf_ProductTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$this->_context = TestHelper::getContext( 'unitperf' );

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search, array( 'text', 'media', 'price', 'product', 'attribute' ) );

		if( ( $this->_item = reset( $result ) ) === false ) {
			throw new Exception( 'No product item found' );
		}
	}


	public function testDetail()
	{
		$start = microtime( true );

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$product = $productManager->getItem( $this->_item->getId(), array( 'text', 'media', 'price', 'product', 'attribute' ) );

		$ids = array();
		foreach( $product->getRefItems( 'product' ) as $subproduct ) {
			$ids[] = $subproduct->getId();
		}

		if( count( $ids ) > 0 )
		{
			$search = $productManager->createSearch( true );
			$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
			$result = $productManager->searchItems( $search, array( 'text', 'media', 'price', 'product', 'attribute' ) );
		}

		$stop = microtime( true );
		echo "\n    product detail: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}

}
