<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Rebuilds the index.
 */
class CatalogRebuildTestIndex extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['AttributeAddTestData', 'ProductAddTestData', 'CatalogAddTestData', 'SupplierAddTestData'];
	}


	/**
	 * Rebuilds the index.
	 */
	public function up()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->context() );

		$this->info( 'Rebuilding index for test data', 'v' );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context() );

		$indexManager->rebuild();
		$indexManager->optimize();
	}
}
