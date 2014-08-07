<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 201
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds default records to plugin table.
 */
class MW_Setup_Task_MShopAddWarehouseDataDefault extends MW_Setup_Task_MShopAddWarehouseData
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
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'MShopAddWarehouseData' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}
}