<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds supplier test data and all items from other domains.
 */
class SupplierAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Media', 'Supplier', 'Text', 'MShopSetLocale'];
	}


	/**
	 * Adds supplier test data.
	 */
	public function up()
	{
		$this->info( 'Adding supplier test data', 'vv' );
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
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'supplier.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the supplier data from the given array
	 *
	 * @param array $testdata Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'supplier' );
		$manager->begin();

		foreach( $testdata['supplier'] ?? [] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $manager, $item, $entry );
			$item = $this->addAddressData( $manager, $item, $entry );

			$manager->save( $item );
		}

		$manager->commit();
	}
}
