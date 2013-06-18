<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ProductAddBasePerfData.php 1316 2012-10-19 19:49:23Z nsendetzky $
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ServiceAddBasePerfData extends MW_Setup_Task_Abstract
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
		return array( 'LocaleAddPerfData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
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
		$this->_msg( 'Adding service base performance data', 0 );


		$manager = MShop_Service_Manager_Factory::createManager( $this->_getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', 'payment' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Service type item "payment" not found' );
		}

		$item = $manager->createItem();
		$item->setTypeId( $typeItem->getId() );
		$item->setProvider( 'PrePay' );
		$item->setStatus( 1 );

		$this->_txBegin();

		for( $i = 0; $i < 100; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item->setId( null );
			$item->setCode( $code );
			$item->setLabel( 'Payment service ' . $code );
			$manager->saveItem( $item, false );
		}

		$this->_txCommit();


		$search = $typeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', 'delivery' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $typeManager->searchItems( $search );

		if ( ( $typeItem = reset( $types ) ) === false ) {
			throw new Exception( 'Service type item "delivery" not found' );
		}

		$item = $manager->createItem();
		$item->setTypeId( $typeItem->getId() );
		$item->setProvider( 'Manual' );
		$item->setStatus( 1 );

		$this->_txBegin();

		for( $i = 0; $i < 100; $i++ )
		{
			$code = 'perf-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

			$item->setId( null );
			$item->setCode( $code );
			$item->setLabel( 'Delivery service ' . $code );
			$manager->saveItem( $item, false );
		}

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
