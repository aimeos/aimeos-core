<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds plugin test data and all items from other domains.
 */
class PluginAddTestData extends \Aimeos\MW\Setup\Task\Base
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
		return [];
	}


	/**
	 * Adds plugin test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding plugin test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addPluginData();

		$this->status( 'done' );
	}


	/**
	 * Adds the plugin test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addPluginData()
	{
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $this->additional, 'Standard' );
		$pluginTypeManager = $pluginManager->getSubManager( 'type', 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'plugin.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for plugin domain', $path ) );
		}

		$plugTypeIds = [];
		$type = $pluginTypeManager->createItem();

		$this->conn->begin();

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
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No plugin type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			$plugin->setId( null );
			$plugin->setTypeId( $plugTypeIds[$dataset['typeid']] );
			$plugin->setLabel( $dataset['label'] );
			$plugin->setStatus( $dataset['status'] );
			$plugin->setConfig( $dataset['config'] );
			$plugin->setProvider( $dataset['provider'] );
			$pluginManager->saveItem( $plugin, false );
		}

		$this->conn->commit();
	}
}