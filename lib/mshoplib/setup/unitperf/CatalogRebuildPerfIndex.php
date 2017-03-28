<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Rebuilds the index.
 */
class CatalogRebuildPerfIndex extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
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
		return [];
	}


	/**
	 * Rebuilds the index.
	 */
	public function migrate()
	{
		$this->msg( 'Rebuilding index for performance data', 0 );

		\Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->rebuildIndex();

		$this->status( 'done' );
	}
}
