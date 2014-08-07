<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ProductAddBasePerfData extends MW_Setup_Task_Abstract
{
	private $_count = 9000;


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
		return array( 'MShopSetLocale' );
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
		$this->_msg('Adding product base performance data', 0);


		$productManager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );
		$productTypeManager = $productManager->getSubManager( 'type' );

		$expr = array();
		$search = $productTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.type.domain', 'product');
		$expr[] = $search->compare('==', 'product.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productTypeManager->searchItems($search);

		if ( ($productTypeItem = reset($types)) === false) {
			throw new Exception('Product type item not found');
		}


		$this->_txBegin();

		$productItem = $productManager->createItem();
		$productItem->setTypeId( $productTypeItem->getId() );
		$productItem->setStatus( 1 );
		$productItem->setSupplierCode( 'My brand' );
		$productItem->setDateStart( '1970-01-01 00:00:00' );

		for( $i = 0; $i < $this->_count; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 5, '0', STR_PAD_LEFT );

			$productItem->setId( null );
			$productItem->setCode( $code );
			$productItem->setLabel( $code );
			$productManager->saveItem( $productItem, false );
		}

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getContext()
	{
		return $this->_additional;
	}


	/**
	 * @param string $domain
	 * @param string $code
	 */
	protected function _getProductListItem( $domain, $code )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/list/type' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.type.code', $code ),
			$search->compare ('==', 'product.list.type.domain', $domain ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$types = $manager->searchItems( $search );

		if( ( $listTypeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Product list type item not found' );
		}


		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/list' );

		$listItem = $manager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( $domain );

		return $listItem;
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
