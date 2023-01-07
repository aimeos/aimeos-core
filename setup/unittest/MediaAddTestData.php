<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds media test data and all items from other domains.
 */
class MediaAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Media', 'MShopSetLocale'];
	}


	/**
	 * Adds media test data.
	 */
	public function up()
	{
		$this->info( 'Adding media test data', 'vv' );
		$this->context()->setEditor( 'core' );

		$this->process( $this->getData() );
	}


	/**
	 * Returns the test data array
	 *
	 * @return array $testdata Multi-dimensional array of test data
	 */
	protected function getData()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'media.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for media domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Adds the media data from the given array
	 *
	 * @param array Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'media' );
		$manager->begin();

		$this->storeTypes( $testdata, ['media/type', 'media/lists/type', 'media/property/type'] );

		$manager->commit();
	}
}
