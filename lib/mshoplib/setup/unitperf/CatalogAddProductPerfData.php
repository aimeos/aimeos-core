<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product performance records to catalog list table.
 */
class MW_Setup_Task_CatalogAddProductPerfData extends MW_Setup_Task_ProductAddBasePerfData
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
	protected function process()
	{
		$this->msg( 'Adding product categories performance data', 0 );


		$context = $this->getContext();

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'list' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.type.domain', 'product' ),
			$search->compare( '==', 'catalog.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Catalog list type item not found' );
		}


		$search = $catalogManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'catalog.level' ), $search->sort( '+', 'catalog.left' ) ) );
		$search->setSlice( 0, 0x7fffffff );

		$catIds = array_keys( $catalogManager->searchItems( $search ) );


		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'product' );


		$this->txBegin();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$start = $pos = 0;

		do
		{
			$catId = current( $catIds );

			if( ( $catId2 = next( $catIds ) ) === false ) {
				$catId2 = reset( $catIds );
			}

			$result = $productManager->searchItems( $search );

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

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->txCommit();


		$this->status( 'done' );
	}
}