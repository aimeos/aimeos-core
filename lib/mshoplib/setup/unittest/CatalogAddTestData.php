<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds catalog test data.
 */
class CatalogAddTestData extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'ProductListAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Adds catalog test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding catalog test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'catalog.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
		}

		$this->addCatalogData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addCatalogData( array $testdata )
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->additional, 'Standard' );

		$parentIds = array( 'init' => null );
		$catalog = $catalogManager->createItem();

		foreach( $testdata['catalog'] as $key => $dataset )
		{
			$catalog->setId( null );
			$catalog->setCode( $dataset['code'] );
			$catalog->setLabel( $dataset['label'] );
			$catalog->setConfig( $dataset['config'] );
			$catalog->setStatus( $dataset['status'] );

			$catalogManager->insertItem( $catalog, $parentIds[$dataset['parent']] );
			$parentIds[$key] = $catalog->getId();
		}
	}
}