<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds attribute test data and all items from other domains.
 */
class AttributeAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Attribute', 'Media', 'Price', 'Text', 'MShopSetLocale'];
	}


	/**
	 * Adds attribute test data.
	 */
	public function up()
	{
		$this->info( 'Adding attribute test data', 'v' );

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
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'attribute.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for attribute domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the product data from the given array
	 *
	 * @param array $testdata Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'attribute' );
		$listManager = $manager->getSubManager( 'lists' );
		$propManager = $manager->getSubManager( 'property' );

		$manager->begin();

		$this->storeTypes( $testdata, ['attribute/type', 'attribute/lists/type', 'attribute/property/type'] );

		foreach( $testdata['attribute'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $listManager, $item, $entry );
			$item = $this->addPropertyData( $propManager, $item, $entry );

			$manager->save( $item );
		}

		$manager->commit();
	}
}
