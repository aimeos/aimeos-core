<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->context() );

		$this->info( 'Adding media test data', 'v' );

		$this->context()->setEditor( 'core:lib/mshoplib' );
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
	 * Returns the manager for the current setup task
	 *
	 * @param string $domain Domain name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( string $domain ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( $domain === 'media' ) {
			return \Aimeos\MShop\Media\Manager\Factory::create( $this->context(), 'Standard' );
		}

		return parent::getManager( $domain );
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
