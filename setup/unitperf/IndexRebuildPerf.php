<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Rebuilds the index.
 */
class IndexRebuildPerf extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Rebuilds the index.
	 */
	public function up()
	{
		$this->info( 'Rebuilding index for performance data', 'vv' );

		\Aimeos\MShop::create( $this->context(), 'index' )->rebuild();
	}
}
