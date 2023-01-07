<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds catalog test data.
 */
class CatalogAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Catalog', 'Media', 'Text', 'MShopSetLocale'];
	}


	/**
	 * Adds catalog test data
	 */
	public function up()
	{
		$this->info( 'Adding catalog test data', 'vv' );
		$this->context()->setEditor( 'core' );

		$this->process( $this->getData() );
	}


	/**
	 * Returns the test data array
	 *
	 * @return array Multi-dimensional array of test data
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'catalog.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for catalog domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the catalog test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param string|null $parentId ID of parent category or null for root category
	 */
	protected function process( array $testdata, $parentId = null )
	{
		$manager = $this->getManager( 'catalog' );

		$manager->begin();
		$this->storeTypes( $testdata, ['catalog/type', 'catalog/lists/type'] );
		$manager->commit();

		foreach( $testdata['catalog'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $manager, $item, $entry );

			$id = $manager->insert( $item, $parentId )->getId();

			if( isset( $entry['catalog'] ) ) {
				$this->process( $entry, $id );
			}
		}
	}
}
