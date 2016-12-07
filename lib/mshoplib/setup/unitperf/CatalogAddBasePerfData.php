<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds performance records to catalog table.
 */
class CatalogAddBasePerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert catalog nodes and product/catalog relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding catalog performance data', 0 );


		$context = $this->getContext();
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );

		$catalogRootItem = $catalogManager->createItem();
		$catalogRootItem->setCode( 'home' );
		$catalogRootItem->setLabel( 'Home' );
		$catalogRootItem->setStatus( 1 );

		$catalogManager->insertItem( $catalogRootItem );

		for( $i = 1; $i <= 5; $i++ )
		{
			$catalogOneItem = $catalogManager->createItem();
			$catalogOneItem->setCode( 'cat-' . $i );
			$catalogOneItem->setLabel( 'cat-' . $i );
			$catalogOneItem->setStatus( 1 );

			$catalogManager->insertItem( $catalogOneItem, $catalogRootItem->getId() );

			for( $j = 1; $j <= 10; $j++ )
			{
				$catalogTwoItem = $catalogManager->createItem();
				$catalogTwoItem->setCode( 'cat-' . $i . ':' . $j );
				$catalogTwoItem->setLabel( 'cat-' . $i . ':' . $j );
				$catalogTwoItem->setStatus( 1 );

				$catalogManager->insertItem( $catalogTwoItem, $catalogOneItem->getId() );

				for( $k = 1; $k <= 10; $k++ )
				{
					$catalogThreeItem = $catalogManager->createItem();
					$catalogThreeItem->setCode( 'cat-' . $i . ':' . $j . ':' . $k );
					$catalogThreeItem->setLabel( 'cat-' . $i . ':' . $j . ':' . $k );
					$catalogThreeItem->setStatus( 1 );

					$catalogManager->insertItem( $catalogThreeItem, $catalogTwoItem->getId() );
				}
			}
		}


		$this->status( 'done' );
	}
}