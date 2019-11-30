<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds admin cache test data.
 */
class CacheAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds admin log test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding admin cache test data', 0 );
		$this->additional->setEditor( 'core:lib/mshoplib' );

		$this->addCacheTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the cache test data.
	 *
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addCacheTestData()
	{
		$manager = \Aimeos\MAdmin\Cache\Manager\Factory::create( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'cache.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for cache domain', $path ) );
		}

		foreach( $testdata['cache'] as $dataset ) {
			$manager->saveItem( $manager->createItem()->fromArray( $dataset, true ), false );
		}
	}

}
