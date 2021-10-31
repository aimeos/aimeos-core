<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo type records to tables.
 */
class DemoAddTypeData extends MShopAddTypeData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale', 'MShopAddTypeData'];
	}


	/**
	 * Executes the task.
	 */
	public function up()
	{
		if( $this->context()->getConfig()->get( 'setup/default/demo', '' ) === '' ) {
			return;
		}

		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'data' . $ds . 'demo-type.php';

		$this->process( $filename );
	}
}
