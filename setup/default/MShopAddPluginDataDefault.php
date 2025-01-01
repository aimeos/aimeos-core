<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 201
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default records to plugin table.
 */
class MShopAddPluginDataDefault extends MShopAddPluginData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddTypeDataDefault'];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	public function up()
	{
		$this->process();
	}
}
