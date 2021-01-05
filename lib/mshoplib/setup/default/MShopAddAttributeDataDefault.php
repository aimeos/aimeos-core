<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds code records to the tables
 */
class MShopAddAttributeDataDefault extends \Aimeos\MW\Setup\Task\MShopAddAttributeData
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
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
