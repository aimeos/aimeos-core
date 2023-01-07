<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
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
		if( $this->context()->config()->get( 'setup/default/demo', '' ) === '' ) {
			return;
		}

		$this->add( __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'demo-type.php' );
	}
}
