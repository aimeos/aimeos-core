<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Rebuilds the catalog index.
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
	 * Rebuilds the catalog index.
	 */
	protected function process()
	{
		$this->msg( 'Rebuilding catalog index for performance data', 0 );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->getContext() );
		$catalogManager->getSubManager( 'index' )->rebuildIndex();

		$this->status( 'done' );
	}
}
