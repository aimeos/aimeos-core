<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Rebuilds the index.
 */
class DemoRebuildIndex extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Index', 'MShopSetLocale'];
	}


	/**
	 * Rebuilds the index.
	 */
	public function up()
	{
		$this->info( 'Rebuilding index for demo data', 'vv' );

		if( $this->context()->config()->get( 'setup/default/demo', '' ) === '' ) {
			return;
		}

		\Aimeos\MShop::create( $this->context(), 'index' )->rebuild()->cleanup( $this->context()->datetime() );
	}
}
