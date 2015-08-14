<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds plugin test data and all items from other domains.
 */
class MW_Setup_Task_PluginAddTestData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ServiceListAddTestData', 'SupplierAddTestData', 'TextListAddTestData' );
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
	 * Adds plugin test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding plugin test data', 0 );
		$this->_additional->setEditor( 'core:unittest' );

		$this->_addPluginData();

		$this->_status( 'done' );
	}


	/**
	 * Adds the plugin test data.
	 *
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function _addPluginData()
	{
		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $this->_additional, 'Default' );
		$pluginTypeManager = $pluginManager->getSubManager( 'type', 'Default' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'plugin.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for plugin domain', $path ) );
		}

		$plugTypeIds = array();
		$type = $pluginTypeManager->createItem();

		$this->_conn->begin();

		foreach( $testdata['plugin/type'] as $key => $dataset )
		{
			$type->setId( null );
			$type->setCode( $dataset['code'] );
			$type->setLabel( $dataset['label'] );
			$type->setDomain( $dataset['domain'] );
			$type->setStatus( $dataset['status'] );

			$pluginTypeManager->saveItem( $type );
			$plugTypeIds[$key] = $type->getId();
		}

		$plugin = $pluginManager->createItem();
		foreach( $testdata['plugin'] as $dataset )
		{
			if( !isset( $plugTypeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No plugin type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$plugin->setId( null );
			$plugin->setTypeId( $plugTypeIds[$dataset['typeid']] );
			$plugin->setLabel( $dataset['label'] );
			$plugin->setStatus( $dataset['status'] );
			$plugin->setConfig( $dataset['config'] );
			$plugin->setProvider( $dataset['provider'] );
			$pluginManager->saveItem( $plugin, false );
		}

		$this->_conn->commit();
	}
}