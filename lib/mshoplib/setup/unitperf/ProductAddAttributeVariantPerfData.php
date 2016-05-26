<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product variant attribute performance records.
 */
class ProductAddAttributeVariantPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert attribute items and product/attribute relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product variant attribute performance data', 0 );


		$this->txBegin();

		$attrListWidth = $this->getAttributeWidthItems();
		$attrListLength = $this->getAttributeLengthItems();

		$this->txCommit();


		$context = $this->getContext();

		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$productListManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );
		$productListTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists/type' );

		$productListTypeItem = $productListTypeManager->findItem( 'variant', array(), 'attribute' );

		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $productListTypeItem->getId() );
		$listItem->setDomain( 'attribute' );


		$this->txBegin();

		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach( $result as $id => $item )
			{
				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( key( $attrListLength ) );
				$listItem->setPosition( 0 );

				$productListManager->saveItem( $listItem, false );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( key( $attrListWidth ) );
				$listItem->setPosition( 1 );

				$productListManager->saveItem( $listItem, false );

				if( next( $attrListLength ) === false )
				{
					reset( $attrListLength );
					next( $attrListWidth );

					if( current( $attrListWidth ) === false ) {
						reset( $attrListWidth );
					}
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->txCommit();


		$this->status( 'done' );
	}


	/**
	 * Creates and returns the attribute width items
	 *
	 * @return array Associative list of IDs as keys and items implementing \Aimeos\MShop\Attribute\Item\Iface as values
	 */
	protected function getAttributeWidthItems()
	{
		$context = $this->getContext();

		$attrManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$attrTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/type' );

		$attrTypeItem = $attrTypeManager->findItem( 'width', array(), 'product' );

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );

		$pos = 0;
		$attrListWidth = array();

		foreach( array( 'tight', 'normal', 'wide' ) as $size )
		{
			$attrItem->setId( null );
			$attrItem->setCode( $size );
			$attrItem->setLabel( $size );
			$attrItem->setPosition( $pos++ );

			$attrManager->saveItem( $attrItem );

			$attrListWidth[$attrItem->getId()] = clone $attrItem;
		}

		return $attrListWidth;
	}


	/**
	 * Creates and returns the attribute length items
	 *
	 * @return array Associative list of IDs as keys and items implementing \Aimeos\MShop\Attribute\Item\Iface as values
	 */
	protected function getAttributeLengthItems()
	{
		$context = $this->getContext();

		$attrManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$attrTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/type' );

		$attrTypeItem = $attrTypeManager->findItem( 'length', array(), 'product' );

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );

		$pos = 0;
		$attrListLength = array();

		foreach( array( 'short', 'normal', 'long' ) as $size )
		{
			$attrItem->setId( null );
			$attrItem->setCode( $size );
			$attrItem->setLabel( $size );
			$attrItem->setPosition( $pos++ );

			$attrManager->saveItem( $attrItem );

			$attrListLength[$attrItem->getId()] = clone $attrItem;
		}

		return $attrListLength;
	}
}
