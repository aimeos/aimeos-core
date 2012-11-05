<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductAddMediaPerfData.php 14535 2011-12-21 16:47:21Z nsendetzky $
 */


/**
 * Adds media performance records.
 */
class MW_Setup_Task_ProductAddMediaPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
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
	 * Insert media items and product/media relations.
	 */
	protected function _process()
	{
		$this->_msg('Adding product media performance data', 0);


		$context = $this->_getContext();

		$mediaManager = MShop_Media_Manager_Factory::createManager( $context );
		$mediaTypeManager = $mediaManager->getSubManager( 'type' );

		$expr = array();
		$search = $mediaTypeManager->createSearch();
		$expr[] = $search->compare('==', 'media.type.domain', 'product');
		$expr[] = $search->compare('==', 'media.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $mediaTypeManager->searchItems($search);

		if ( ($mediaTypeItem = reset($types)) === false) {
			throw new Exception('Media type item not found');
		}


		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.domain', 'media');
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($productListTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$search = $productManager->createSearch();

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $productListTypeItem->getId() );
		$listItem->setDomain( 'media' );

		$mediaItem = $mediaManager->createItem();
		$mediaItem->setTypeId( $mediaTypeItem->getId() );
		$mediaItem->setLanguageId( null );
		$mediaItem->setDomain( 'product' );
		$mediaItem->setMimeType( 'image/jpeg' );
		$mediaItem->setStatus( 1 );


		$this->_txBegin();

		$start = $pos = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $item )
			{
				$mediaItem->setId( null );
				$mediaItem->setLabel( '1. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( '../images/unitperf/' . ( ($pos + 0) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( '../images/unitperf/' . ( ($pos + 0) % 4 + 1 ) . '.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 0 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '2. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( '../images/unitperf/' . ( ($pos + 1) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( '../images/unitperf/' . ( ($pos + 1) % 4 + 1 ) . '.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 1 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '3. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( '../images/unitperf/' . ( ($pos + 2) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( '../images/unitperf/' . ( ($pos + 2) % 4 + 1 ) . '.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 2 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '4. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( '../images/unitperf/' . ( ($pos + 3) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( '../images/unitperf/' . ( ($pos + 3) % 4 + 1 ) . '.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 3 );
				$productListManager->saveItem( $listItem, false );


				$pos++;
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
