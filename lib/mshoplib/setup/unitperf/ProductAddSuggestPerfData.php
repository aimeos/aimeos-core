<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds suggestion performance records to products.
 */
class MW_Setup_Task_ProductAddSuggestPerfData extends MW_Setup_Task_Abstract
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
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'ProductAddSelectPerfData' );
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
		$this->_msg( 'Adding product suggestion performance data', 0 );


		$productManager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );
		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'suggestion');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'product');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}

		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$refsearch = $productManager->createSearch();
		$refsearch->setSortations( array( $refsearch->sort( '+', 'product.id' ) ) );
		$refsearch->setSlice( 1 );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'product' );


		$this->_txBegin();

		$start = 0;

		do
		{
			$num = 0;

			$result = $productManager->searchItems( $search );
			$refresult = $productManager->searchItems( $refsearch );

			foreach( $result as $id => $product )
			{
				$pos = 0;
				$length = ( $num % 4 ) + 3;

				foreach( array_slice( $refresult, $num, $length, true ) as $refid => $refproduct )
				{
					$listItem->setId( null );
					$listItem->setParentId( $id );
					$listItem->setRefId( $refid );
					$listItem->setPosition( $pos++ );

					$productListManager->saveItem( $listItem, false );
				}

				$num++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
			$refsearch->setSlice( $start + 1 );
		}
		while( $count == $search->getSliceSize() );

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
