<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds service test data.
 */
class ServiceAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Service', 'Media', 'Price', 'Text', 'MShopSetLocale', 'CustomerAddTestData'];
	}


	/**
	 * Adds service test data.
	 */
	public function up()
	{
		$this->info( 'Adding service test data', 'v' );

		$this->context()->setEditor( 'core:lib/mshoplib' );
		$this->process( $this->getData() );
	}


	/**
	 * Returns the test data array
	 *
	 * @return array Multi-dimensional array of test data
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'service.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for service domain', $path ) );
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
		$manager = $this->getManager( 'service' );
		$listManager = $manager->getSubManager( 'lists' );

		$manager->begin();

		$this->storeTypes( $testdata, ['service/type', 'service/lists/type'] );

		foreach( $testdata['service'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $listManager, $item, $entry );

			$manager->save( $item );
		}

		$manager->commit();
	}
}
