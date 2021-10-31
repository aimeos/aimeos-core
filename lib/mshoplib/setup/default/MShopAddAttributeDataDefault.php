<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds code records to the tables
 */
class MShopAddAttributeDataDefault extends MShopAddAttributeData
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale', 'MShopAddTypeDataDefault'];
	}


	/**
	 * Executes the task
	 */
	public function migrate()
	{
		$this->process();
	}
}
