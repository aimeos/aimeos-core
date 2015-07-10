<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds performance records to catalog table.
 */
class MW_Setup_Task_CatalogAddBasePerfData extends MW_Setup_Task_ProductAddBasePerfData
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
	protected function _process()
	{
		$this->_msg('Adding catalog performance data', 0);


		$context =  $this->_getContext();
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );

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


		$this->_status( 'done' );
	}
}