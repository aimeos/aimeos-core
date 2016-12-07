<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product promotion performance records to catalog list table.
 */
class CatalogAddPromoPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
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
	public function migrate()
	{
		$this->msg( 'Adding catalog promotion performance data', 0 );


		$context = $this->getContext();

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'lists' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.type.domain', 'product' ),
			$search->compare( '==', 'catalog.lists.type.code', 'promotion' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Catalog list type item not found' );
		}


		$search = $catalogManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'catalog.level' ), $search->sort( '+', 'catalog.left' ) ) );
		$search->setSlice( 0, 1000 );

		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'product' );


		$start = 0;

		do
		{
			$this->txBegin();

			$result = $catalogManager->searchItems( $search );

			foreach( $result as $catId => $catItem )
			{
				$pos = 0;

				$search = $catalogListManager->createSearch();
				$expr = array(
					$search->compare( '==', 'catalog.lists.parentid', $catId ),
					$search->compare( '==', 'catalog.lists.position', array( 20, 40, 60, 80 ) ),
					$search->compare( '==', 'catalog.lists.domain', 'product' ),
					$search->compare( '==', 'catalog.lists.type.code', 'default' ),
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
			$search->setSlice( $start, 1000 );

			$this->txCommit();
		}
		while( $count == $search->getSliceSize() );


		$this->status( 'done' );
	}
}