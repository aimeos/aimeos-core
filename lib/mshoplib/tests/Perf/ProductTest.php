<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\Perf;


class ProductTest extends \PHPUnit\Framework\TestCase
{
	private $item;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext( 'unitperf' );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search, array( 'text', 'media', 'price', 'product', 'attribute' ) );

		if( ( $this->item = $result->first() ) === null ) {
			throw new \RuntimeException( 'No product item found' );
		}
	}


	public function testDetail()
	{
		$start = microtime( true );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->getItem( $this->item->getId(), array( 'text', 'media', 'price', 'product', 'attribute' ) );

		$ids = [];
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
