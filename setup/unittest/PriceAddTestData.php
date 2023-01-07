<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds price test data.
 */
class PriceAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Price', 'MShopSetLocale'];
	}


	/**
	 * Adds price test data.
	 */
	public function up()
	{
		$this->info( 'Adding price test data', 'vv' );
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
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'price.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for price domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the price data from the given array
	 *
	 * @param array $testdata Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'price' );
		$manager->begin();

		$this->storeTypes( $testdata, ['price/type', 'price/lists/type', 'price/property/type'] );

		$manager->commit();
	}
}
