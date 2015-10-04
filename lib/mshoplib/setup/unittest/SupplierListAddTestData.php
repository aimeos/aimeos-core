<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds supplier list test data.
 */
class MW_Setup_Task_SupplierListAddTestData extends MW_Setup_Task_Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'TextAddTestData', 'ProductAddTestData', 'SupplierAddTestData' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds supplier test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding supplier-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'supplier-list.php';

		if( ( $testdata = include( $path ) ) == false ){
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for supplier list domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['supplier/list'] as $dataset ) {
			$refKeys[ $dataset['domain'] ][] = $dataset['refid'];
		}

		$refIds = array();
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		//$refIds['product'] = $this->getProductData( $refKeys['product'] );
		$this->addSupplierListData( $testdata, $refIds );

		$this->status( 'done' );
	}


	/**
	 * Gets required product item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getProductData( array $keys )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->additional, 'Standard' );

		$codes = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos+1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref products are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		$refIds = array();
		foreach( $manager->searchItems( $search ) as $item ) {
			$refIds[ 'product/' . $item->getCode() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Gets required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getTextData( array $keys )
	{
		$textManager = MShop_Text_Manager_Factory::createManager( $this->additional, 'Standard' );

		$labels = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos+1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = array();
		foreach( $textManager->searchItems( $search ) as $item )	{
			$refIds[ 'text/'.$item->getLabel() ] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the supplier-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param array $refIds Associative list of domains and the keys/IDs of the inserted items
	 * @param string $type Manager type string
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	protected function addSupplierListData( array $testdata, array $refIds, $type = 'Standard' )
	{
		$supplierManager = MShop_Supplier_Manager_Factory::createManager( $this->additional, $type );
		$supplierListManager = $supplierManager->getSubManager( 'list', $type );
		$supplierListTypeManager = $supplierListManager->getSubmanager( 'type', $type );

		$itemCode = array();
		foreach( $testdata['supplier/list'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false || ( $str = substr( $dataset['parentid'], $pos+1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$itemCode[] = $str;
		}

		$search = $supplierManager->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.code', $itemCode) );

		$parentIds = array();
		foreach( $supplierManager->searchItems( $search ) as $item )	{
			$parentIds[ 'supplier/'.$item->getCode() ] = $item->getId();
		}

		$listItemTypeIds = array();
		$listItemType = $supplierListTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['supplier/list/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$supplierListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[ $key ] = $listItemType->getId();
		}

		$listItem = $supplierListManager->createItem();
		foreach( $testdata['supplier/list'] as $dataset )
		{
			if( !isset( $parentIds[ $dataset['parentid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No supplier ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $refIds[ $dataset['domain'] ][ $dataset['refid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%2$s" ref ID found for "%1$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			if( !isset( $listItemTypeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No supplier list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[ $dataset['parentid'] ] );
			$listItem->setTypeId( $listItemTypeIds[ $dataset['typeid'] ] );
			$listItem->setRefId( $refIds[ $dataset['domain'] ] [ $dataset['refid'] ] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$supplierListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}
}