<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds default records plugin to table.
 */
class MShopAddPluginData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
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
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		// executed by tasks in sub-directories for specific sites
	}


	/**
	 * Adds locale data.
	 */
	protected function process()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding default plugin data', 0 );
		$this->status( '' );


		$ds = DIRECTORY_SEPARATOR;
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $this->additional, 'Standard' );


		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'plugin.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'No data file "%1$s" found', $filename ) );
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
		$this->msg( 'Adding data for MShop plugins', 1 );

		$types = [];
		$manager = $pluginManager->getSubManager( 'type' );

		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
			$types['plugin/' . $item->getCode()] = $item;
		}

		$num = $total = 0;
		$item = $pluginManager->createItem();

		foreach( $data as $key => $dataset )
		{
			$total++;

			if( !isset( $types[$dataset['typeid']] ) ) {
				throw new \RuntimeException( sprintf( 'No plugin type "%1$s" found', $dataset['typeid'] ) );
			}

			$item->setId( null );
			$item->setTypeId( $types[$dataset['typeid']]->getId() );
			$item->setProvider( $dataset['provider'] );
			$item->setLabel( $dataset['label'] );
			$item->setConfig( $dataset['config'] );
			$item->setStatus( $dataset['status'] );

			if( isset( $dataset['position'] ) ) {
				$item->setPosition( $dataset['position'] );
			}

			try {
				$pluginManager->saveItem( $item );
				$num++;
			} catch( \Exception $e ) {; } // if plugin configuration was already available
		}

		$this->status( $num > 0 ? $num . '/' . $total : 'OK' );
	}
}