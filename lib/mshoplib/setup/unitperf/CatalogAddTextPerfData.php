<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds text performance records to catalog list table.
 */
class CatalogAddTextPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddBasePerfData', 'MShopAddTypeDataUnitperf' );
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
		$this->msg( 'Adding catalog text performance data', 0 );


		$context = $this->getContext();

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'lists' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.type.domain', 'text' ),
			$search->compare( '==', 'catalog.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Catalog list type item not found' );
		}


		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $context );
		$textTypeManager = $textManager->getSubManager( 'type' );

		$search = $textTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'text.type.domain', 'catalog' ),
			$search->compare( '==', 'text.type.code', 'name' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $textTypeManager->searchItems( $search );

		if( ( $textTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Text type item not found' );
		}


		$textItem = $textManager->createItem();
		$textItem->setTypeId( $textTypeItem->getId() );
		$textItem->setLanguageId( 'en' );
		$textItem->setDomain( 'catalog' );
		$textItem->setStatus( 1 );


		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'text' );


		$this->txBegin();

		$start = $pos = 0;
		$search = $catalogManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'catalog.id' ) ) );

		do
		{
			$result = $catalogManager->searchItems( $search );

			foreach( $result as $id => $item )
			{
				$textItem->setId( null );
				$textItem->setLabel( $item->getLabel() );
				$textItem->setContent( str_replace( '-', ' ', $item->getLabel() ) );

				$textManager->saveItem( $textItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $textItem->getId() );
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