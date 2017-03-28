<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds catalog list test data and all items from other domains.
 */
class CatalogListAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductListAddTestData', 'CatalogAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Adds catalog test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding catalog-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'catalog-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for catalog list domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['catalog/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$refIds = [];
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$refIds['media'] = $this->getMediaData( $refKeys['media'] );
		$refIds['product'] = $this->getProductData( $refKeys['product'] );

		$this->addCatalogListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function getTextData( array $keys )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->additional, 'Standard' );

		$labels = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = [];
		foreach( $textManager->searchItems( $search ) as $item ) {
			$refIds['text/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function getMediaData( array $keys )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->additional, 'Standard' );

		$urls = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$refIds = [];
		foreach( $mediaManager->searchItems( $search ) as $item ) {
			$refIds['media/' . $item->getUrl()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required product item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no product is found
	 */
	private function getProductData( array $keys )
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );

		$codes = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref product are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		$refIds = [];
		foreach( $productManager->searchItems( $search ) as $item ) {
			$refIds['product/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addCatalogListData( array $testdata, array $refIds )
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->additional, 'Standard' );
		$catalogListManager = $catalogManager->getSubManager( 'lists', 'Standard' );
		$catalogListTypeManager = $catalogListManager->getSubManager( 'type', 'Standard' );

		$itemCode = [];
		foreach( $testdata['catalog/lists'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$itemCode[] = $str;
		}

		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $itemCode ) );

		$parentIds = [];
		foreach( $catalogManager->searchItems( $search ) as $item ) {
			$parentIds['catalog/' . $item->getCode()] = $item->getId();
		}

		$listItemTypeIds = [];
		$listItemType = $catalogListTypeManager->createItem();

		foreach( $testdata['catalog/lists/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$catalogListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		$this->conn->begin();

		$listItem = $catalogListManager->createItem();
		foreach( $testdata['catalog/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No catalog ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No catalog list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[$dataset['parentid']] );
			$listItem->setTypeId( $listItemTypeIds[$dataset['typeid']] );
			$listItem->setRefId( $refIds[$dataset['domain']][$dataset['refid']] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$catalogListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}
}