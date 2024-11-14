<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default records plugin to table.
 */
class MShopAddPluginData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Plugin'];
	}


	public function up()
	{
	}


	/**
	 * Adds locale data.
	 */
	protected function process()
	{
		$this->info( 'Adding default plugin data', 'vv' );

		$ds = DIRECTORY_SEPARATOR;
		$pluginManager = \Aimeos\MShop::create( $this->context(), 'plugin', 'Standard' );

		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'plugin.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['plugin'] ) ) {
			$this->addPluginData( $pluginManager, $data['plugin'] );
		}
	}


	/**
	 * Adds plugin data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $pluginManager Plugin manager
	 * @param array $data Associative list of plugin data
	 */
	protected function addPluginData( \Aimeos\MShop\Common\Manager\Iface $pluginManager, array $data )
	{
		$this->info( 'Adding data for MShop plugins', 'vv' );

		$types = [];
		$manager = $pluginManager->getSubManager( 'type' );

		foreach( $manager->search( $manager->filter() ) as $item ) {
			$types['plugin/' . $item->getCode()] = $item;
		}

		$item = $pluginManager->create();

		foreach( $data as $key => $dataset )
		{
			$item->setId( null );
			$item->setType( $dataset['type'] );
			$item->setProvider( $dataset['provider'] );
			$item->setLabel( $dataset['label'] );
			$item->setConfig( $dataset['config'] );
			$item->setStatus( $dataset['status'] );

			if( isset( $dataset['position'] ) ) {
				$item->setPosition( $dataset['position'] );
			}

			try {
				$pluginManager->save( $item );
			} catch( \Exception $e ) {; } // if plugin configuration was already available
		}
	}
}
