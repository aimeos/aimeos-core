<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds demo type records to tables.
 */
class DemoAddTypeData extends \Aimeos\MW\Setup\Task\MShopAddTypeData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale', 'MShopAddTypeData'];
	}


	/**
	 * Executes the task.
	 */
	public function migrate()
	{
		if( $this->getContext()->getConfig()->get( 'setup/default/demo', '' ) === '' ) {
			return;
		}

		$this->process();
	}
}
