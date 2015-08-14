<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product promotion performance records to catalog list table.
 */
class MW_Setup_Task_CatalogAddPromoPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddProductPerfData' );
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
	 * Inserts catalog promotion products.
	 */
	protected function _process()
	{
		$this->_msg( 'Adding catalog promotion performance data', 0 );


		$context = $this->_getContext();

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'list' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.type.domain', 'product' ),
			$search->compare( '==', 'catalog.list.type.code', 'promotion' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Catalog list type item not found' );
		}


		$search = $catalogManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'catalog.level' ), $search->sort( '+', 'catalog.left' ) ) );


		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'product' );


		$start = 0;

		do
		{
			$this->_txBegin();

			$result = $catalogManager->searchItems( $search );

			foreach( $result as $catId => $catItem )
			{
				$pos = 0;

				$search = $catalogListManager->createSearch();
				$expr = array(
					$search->compare( '==', 'catalog.list.parentid', $catId ),
					$search->compare( '==', 'catalog.list.position', array( 20, 40, 60, 80 ) ),
					$search->compare( '==', 'catalog.list.domain', 'product' ),
					$search->compare( '==', 'catalog.list.type.code', 'default' ),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				foreach( $catalogListManager->searchItems( $search ) as $item )
				{
					$listItem->setId( null );
					$listItem->setParentId( $item->getParentId() );
					$listItem->setRefId( $item->getRefId() );
					$listItem->setPosition( $pos++ );

					$catalogListManager->saveItem( $listItem, false );
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );

			$this->_txCommit();
		}
		while( $count == $search->getSliceSize() );


		$this->_status( 'done' );
	}
}