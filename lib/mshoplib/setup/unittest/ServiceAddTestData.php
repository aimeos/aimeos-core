<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds service test data.
 */
class MW_Setup_Task_ServiceAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'CatalogListAddTestData' );
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
	 * Adds service test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding service test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'service.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$this->_addServiceData( $testdata );

		$this->_status( 'done' );
	}


	/**
	 * Adds the service test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function _addServiceData( array $testdata )
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_additional, 'Default' );
		$serviceTypeManager = $serviceManager->getSubManager( 'type', 'Default' );

		$typeIds = array();
		$type = $serviceTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['service/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$serviceTypeManager->saveItem( $type );
			$typeIds[ $key ] = $type->getId();
		}

		$parentIds = array ();
		$parent = $serviceManager->createItem();
		foreach( $testdata['service'] as $key => $dataset )
		{
			if( !isset( $typeIds[ $dataset['typeid'] ] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No service type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$parent->setId( null );
			$parent->setTypeId( $typeIds[ $dataset['typeid'] ] );
			$parent->setPosition( $dataset['pos'] );
			$parent->setCode( $dataset['code'] );
			$parent->setLabel( $dataset['label'] );
			$parent->setProvider( $dataset['provider'] );
			$parent->setConfig( $dataset['config'] );
			$parent->setStatus( $dataset['status'] );

			$serviceManager->saveItem( $parent, false );
		}

		$this->_conn->commit();
	}
}