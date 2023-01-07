<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds default records to tables.
 */
class MShopAddTypeDataDefault extends MShopAddTypeData
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


	public function up()
	{
		$this->add();
	}
}
