<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds service test data.
 */
class MW_Setup_Task_ServiceAddTestData extends MW_Setup_Task_Base
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
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds service test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding service test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'service.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for service domain', $path ) );
		}

		$this->addServiceData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the service test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addServiceData( array $testdata )
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->additional, 'Standard' );
		$serviceTypeManager = $serviceManager->getSubManager( 'type', 'Standard' );

		$typeIds = array();
		$type = $serviceTypeManager->createItem();

		$this->conn->begin();

		foreach( $testdata['service/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setDomain( $dataset['domain'] );
			$type->setLabel( $dataset['label'] );
			$type->setStatus( $dataset['status'] );

			$serviceTypeManager->saveItem( $type );
			$typeIds[$key] = $type->getId();
		}

		$parent = $serviceManager->createItem();

		foreach( $testdata['service'] as $key => $dataset )
		{
			if( !isset( $typeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No service type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$parent->setId( null );
			$parent->setTypeId( $typeIds[$dataset['typeid']] );
			$parent->setPosition( $dataset['pos'] );
			$parent->setCode( $dataset['code'] );
			$parent->setLabel( $dataset['label'] );
			$parent->setProvider( $dataset['provider'] );
			$parent->setConfig( $dataset['config'] );
			$parent->setStatus( $dataset['status'] );

			$serviceManager->saveItem( $parent, false );
		}

		$this->conn->commit();
	}
}