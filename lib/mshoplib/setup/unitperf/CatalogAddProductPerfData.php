<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product performance records to catalog list table.
 */
class CatalogAddProductPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddBasePerfData', 'MShopAddTypeDataUnitperf', 'ProductAddBasePerfData', 'ProductAddSelectPerfData' );
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
	 * Insert catalog nodes and product/catalog relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product categories performance data', 0 );


		$context = $this->getContext();

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'lists' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.type.domain', 'product' ),
			$search->compare( '==', 'catalog.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Catalog list type item not found' );
		}


		$search = $catalogManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'catalog.level' ), $search->sort( '+', 'catalog.left' ) ) );
		$search->setSlice( 0, 0x7fffffff );

		$catIds = array_keys( $catalogManager->searchItems( $search ) );


		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'product' );


		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$start = $pos = 0;

		do
		{
			$catId = current( $catIds );

			if( ( $catId2 = next( $catIds ) ) === false ) {
				$catId2 = reset( $catIds );
			}

			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $id => $item )
			{
				$listItem->setId( null );
				$listItem->setParentId( $catId );
				$listItem->setRefId( $id );
				$listItem->setPosition( $pos++ );

				$catalogListManager->saveItem( $listItem, false );

				$listItem->setId( null );
				$listItem->setParentId( $catId2 );
				$listItem->setRefId( $id );
				$listItem->setPosition( $pos++ );

				$catalogListManager->saveItem( $listItem, false );
			}

			$this->txCommit();

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 1000 );
		}
		while( $count == $search->getSliceSize() );


		$this->status( 'done' );
	}
}