<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds plugin test data and all items from other domains.
 */
class PluginAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Plugin', 'MShopSetLocale'];
	}


	/**
	 * Adds plugin test data.
	 */
	public function up()
	{
		$this->info( 'Adding plugin test data', 'v' );
		$this->context()->setEditor( 'core:lib/mshoplib' );

		$this->addPluginData();
	}


	/**
	 * Adds the plugin test data.
	 *
	 * @throws \RuntimeException If no type ID is found
	 */
	private function addPluginData()
	{
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::create( $this->context(), 'Standard' );
		$pluginTypeManager = $pluginManager->getSubManager( 'type', 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'plugin.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for plugin domain', $path ) );
		}

		$pluginManager->begin();

		foreach( $testdata['plugin/type'] as $dataset ) {
			$pluginTypeManager->save( $pluginTypeManager->create()->fromArray( $dataset ), false );
		}

		foreach( $testdata['plugin'] as $dataset ) {
			$pluginManager->save( $pluginManager->create()->fromArray( $dataset ), false );
		}

		$pluginManager->commit();
	}
}
