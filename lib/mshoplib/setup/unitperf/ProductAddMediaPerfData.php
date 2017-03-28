<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds media performance records.
 */
class ProductAddMediaPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
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
	 * Insert media items and product/media relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product media performance data', 0 );


		$context = $this->getContext();
		$prefix = 'http://demo.aimeos.org/media/';


		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $context );
		$mediaTypeManager = $mediaManager->getSubManager( 'type' );

		$expr = [];
		$search = $mediaTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'media.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'media.type.code', 'default' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $mediaTypeManager->searchItems( $search );

		if( ( $mediaTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Media type item not found' );
		}


		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'lists' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = [];
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'media' );
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'default' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems( $search );

		if( ( $productListTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Product list type item not found' );
		}

		$expr = [];
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'media' );
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'download' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems( $search );

		if( ( $downloadListTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Product list type item not found' );
		}


		$mediaItem = $mediaManager->createItem();
		$mediaItem->setTypeId( $mediaTypeItem->getId() );
		$mediaItem->setLanguageId( null );
		$mediaItem->setDomain( 'product' );
		$mediaItem->setMimeType( 'image/jpeg' );
		$mediaItem->setStatus( 1 );

		$downloadItem = $mediaManager->createItem();
		$downloadItem->setTypeId( $mediaTypeItem->getId() );
		$downloadItem->setLanguageId( null );
		$downloadItem->setDomain( 'product' );
		$downloadItem->setMimeType( 'application/pdf' );
		$downloadItem->setLabel( 'PDF download' );
		$downloadItem->setPreview( $prefix . 'unitperf/download-preview.jpg' );
		$downloadItem->setUrl( $prefix . 'unitperf/download.pdf' );
		$downloadItem->setStatus( 1 );

		$mediaManager->saveItem( $downloadItem );


		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $productListTypeItem->getId() );
		$listItem->setDomain( 'media' );

		$downloadListItem = $productListManager->createItem();
		$downloadListItem->setTypeId( $downloadListTypeItem->getId() );
		$downloadListItem->setRefId( $downloadItem->getId() );
		$downloadListItem->setDomain( 'media' );
		$downloadListItem->setPosition( 0 );


		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );


		$start = $pos = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $id => $item )
			{
				$mediaItem->setId( null );
				$mediaItem->setLabel( '1. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( $prefix . 'unitperf/' . ( ( $pos + 0 ) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( $prefix . 'unitperf/' . ( ( $pos + 0 ) % 4 + 1 ) . '-big.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 0 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '2. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( $prefix . 'unitperf/' . ( ( $pos + 1 ) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( $prefix . 'unitperf/' . ( ( $pos + 1 ) % 4 + 1 ) . '-big.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 1 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '3. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( $prefix . 'unitperf/' . ( ( $pos + 2 ) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( $prefix . 'unitperf/' . ( ( $pos + 2 ) % 4 + 1 ) . '-big.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 2 );
				$productListManager->saveItem( $listItem, false );


				$mediaItem->setId( null );
				$mediaItem->setLabel( '4. picture for ' . $item->getLabel() );
				$mediaItem->setPreview( $prefix . 'unitperf/' . ( ( $pos + 3 ) % 4 + 1 ) . '.jpg' );
				$mediaItem->setUrl( $prefix . 'unitperf/' . ( ( $pos + 3 ) % 4 + 1 ) . '-big.jpg' );
				$mediaManager->saveItem( $mediaItem );

				$listItem->setId( null );
				$listItem->setParentId( $id );
				$listItem->setRefId( $mediaItem->getId() );
				$listItem->setPosition( 3 );
				$productListManager->saveItem( $listItem, false );


				$pos++;


				$downloadListItem->setId( null );
				$downloadListItem->setParentId( $id );
				$productListManager->saveItem( $downloadListItem, false );
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
