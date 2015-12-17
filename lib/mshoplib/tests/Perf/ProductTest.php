<?php

namespace Aimeos\Perf;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
	private $item;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext( 'unitperf' );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search, array( 'text', 'media', 'price', 'product', 'attribute' ) );

		if( ( $this->item = reset( $result ) ) === false ) {
			throw new \Exception( 'No product item found' );
		}
	}


	public function testDetail()
	{
		$start = microtime( true );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$product = $productManager->getItem( $this->item->getId(), array( 'text', 'media', 'price', 'product', 'attribute' ) );

		$ids = array();
		foreach( $product->getRefItems( 'product' ) as $subproduct ) {
			$ids[] = $subproduct->getId();
		}

		if( count( $ids ) > 0 )
		{
			$search = $productManager->createSearch( true );
			$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
			$productManager->searchItems( $search, array( 'text', 'media', 'price', 'product', 'attribute' ) );
		}

		$stop = microtime( true );
		echo "\n    product detail: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}

}
