<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Rebuilds the catalog index.
 */
class MW_Setup_Task_CatalogRebuildPerfIndex extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
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
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Rebuilds the catalog index.
	 */
	protected function _process()
	{
		$this->_msg('Rebuilding catalog index for performance data', 0);

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_getContext() );
		$catalogManager->getSubManager( 'index' )->rebuildIndex();

		$this->_status( 'done' );
	}
}
