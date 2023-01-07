<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds product test data
 */
class ProductAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Media', 'Price', 'Product', 'Text', 'AttributeAddTestData', 'CatalogAddTestData', 'SupplierAddTestData', 'TagAddTestData'];
	}


	/**
	 * Adds product test data
	 */
	public function up()
	{
		$this->info( 'Adding product test data', 'vv' );
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
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'product.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the product data from the given array
	 *
	 * @param array Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'product' );

		$manager->begin();
		$this->storeTypes( $testdata, ['product/type', 'product/lists/type', 'product/property/type'] );
		$manager->commit();

		foreach( $testdata['product'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $manager, $item, $entry );
			$item = $this->addPropertyData( $manager, $item, $entry );

			$manager->save( $item );
		}
	}
}
