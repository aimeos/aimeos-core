<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductAddCatalogPerfData.php 14535 2011-12-21 16:47:21Z nsendetzky $
 */


/**
 * Adds text performance records to catalog list table.
 */
class MW_Setup_Task_CatalogAddTextPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'CatalogAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert catalog nodes and product/catalog relations.
	 */
	protected function _process()
	{
		$this->_msg('Adding catalog text performance data', 0);


		$context =  $this->_getContext();

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catalogListManager = $catalogManager->getSubManager( 'list' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type' );


		$search = $catalogListTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.type.domain', 'text' ),
			$search->compare( '==', 'catalog.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $catalogListTypeManager->searchItems( $search );

		if ( ( $typeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Catalog list type item not found' );
		}


		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$textTypeManager = $textManager->getSubManager( 'type' );

		$search = $textTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'text.type.domain', 'catalog' ),
			$search->compare( '==', 'text.type.code', 'name' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $textTypeManager->searchItems( $search );

		if ( ( $textTypeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Text type item not found' );
		}


		$textItem = $textManager->createItem();
		$textItem->setTypeId( $textTypeItem->getId() );
		$textItem->setLanguageId( 'en' );
		$textItem->setDomain( 'catalog' );
		$textItem->setStatus( 1 );


		$listItem = $catalogListManager->createItem();
		$listItem->setTypeId( $typeItem->getId() );
		$listItem->setDomain( 'text' );


		$this->_txBegin();

		$start = $pos = 0;
		$search = $catalogManager->createSearch();

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
		while( $count > 0 );

		$this->_txCommit();


		$this->_status( 'done' );
	}
}