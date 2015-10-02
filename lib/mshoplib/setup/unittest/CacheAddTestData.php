<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds admin cache test data.
 */
class MW_Setup_Task_CacheAddTestData extends MW_Setup_Task_Abstract
{
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
	 * Adds admin log test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding admin cache test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addCacheTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the cache test data.
	 *
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addCacheTestData()
	{
		$manager = MAdmin_Cache_Manager_Factory::createManager( $this->additional, 'Default' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'cache.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for cache domain', $path ) );
		}

		$item = $manager->createItem();

		foreach( $testdata['cache'] as $dataset )
		{
			$item->setId( $dataset['id'] );
			$item->setValue( $dataset['value'] );
			$item->setTimeExpire( $dataset['expire'] );
			$item->setTags( $dataset['tags'] );

			$manager->saveItem( $item, false );
		}
	}

}