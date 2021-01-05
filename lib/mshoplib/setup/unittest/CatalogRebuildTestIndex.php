<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Rebuilds the index.
 */
class CatalogRebuildTestIndex extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['AttributeAddTestData', 'ProductAddTestData', 'CatalogAddTestData', 'SupplierAddTestData'];
	}


	/**
	 * Rebuilds the index.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Rebuilding index for test data', 0 );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->additional );

		$indexManager->rebuild();
		$indexManager->optimize();

		$this->status( 'done' );
	}
}
