<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds selection performance records to product table.
 */
class MW_Setup_Task_ProductAddSelectPerfData extends MW_Setup_Task_Abstract
{
	public function __construct( MW_Setup_DBSchema_Interface $schema, MW_DB_Connection_Interface $conn, $additional = null )
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddPerfData', 'ProductAddBasePerfData', 'ProductAddTextPerfData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'ProductAddMediaPerfData', 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert product data.
	 */
	protected function _process()
	{
		$this->_msg('Adding product selection performance data', 0);


		$productManager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );
		$productTypeManager = $productManager->getSubManager( 'type' );

		$expr = array();
		$search = $productTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.type.domain', 'product');
		$expr[] = $search->compare('==', 'product.type.code', 'select');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productTypeManager->searchItems($search);

		if ( ($productTypeItem = reset($types)) === false) {
			throw new Exception('Product type item not found');
		}


		$this->_txBegin();

		$productItem = $productManager->createItem();
		$productItem->setTypeId( $productTypeItem->getId() );
		$productItem->setStatus( 1 );
		$productItem->setSupplierCode( 'My selection brand' );
		$productItem->setDateStart( '1970-01-01 00:00:00' );

		$selProducts = array();

		for( $i = 0; $i < 1000; $i++ )
		{
			$productItem->setId( null );
			$productItem->setCode( 'perf-select-' . str_pad( $i, 5, '0', STR_PAD_LEFT ) );
			$productItem->setLabel( 'Selection product ' . ($i+1) );
			$productManager->saveItem( $productItem );

			$selProducts[] = $productItem->getId();
		}

		$this->_txCommit();


		$expr = array();
		$search = $productTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.type.domain', 'product');
		$expr[] = $search->compare('==', 'product.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productTypeManager->searchItems($search);

		if ( ($productTypeItem = reset($types)) === false) {
			throw new Exception('Product type item not found');
		}

		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'product');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.typeid', $productTypeItem->getId() ) );
		$search->setSortations( array( $search->sort( '-', 'product.id' ) ) );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'product' );


		$this->_txBegin();

		$start = $num = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach( $result as $id => $product )
			{
				$pos = (int) ( $num / 9 );

				$listItem->setId( null );
				$listItem->setParentId( $selProducts[ $pos ] );
				$listItem->setRefId( $id );

				$productListManager->saveItem( $listItem, false );

				$num++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getContext()
	{
		return $this->_additional;
	}


	protected function _txBegin()
	{
		$dbm = $this->_additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	protected function _txCommit()
	{
		$dbm = $this->_additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}
