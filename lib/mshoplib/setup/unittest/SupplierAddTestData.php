<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
		return ['Media', 'Supplier', 'Text', 'MShopSetLocale', 'ProductAddTestData'];
	}


	/**
	 * Adds supplier test data.
	 */
	public function up()
	{
		$this->info( 'Adding supplier test data', 'v' );

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
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'supplier.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
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
		if( $domain === 'supplier' ) {
			return \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context(), 'Standard' );
		}

		return parent::getManager( $domain );
	}


	/**
	 * Adds the supplier data from the given array
	 *
	 * @param array $testdata Multi-dimensional array of test data
	 */
	protected function process( array $testdata )
	{
		$manager = $this->getManager( 'supplier' );
		$listManager = $manager->getSubManager( 'lists' );
		$addrManager = $manager->getSubManager( 'address' );

		$manager->begin();
		$this->storeTypes( $testdata, ['supplier/lists/type'] );
		$manager->commit();

		foreach( $testdata['supplier'] ?? [] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $listManager, $item, $entry );
			$item = $this->addAddressData( $addrManager, $item, $entry );

			$manager->save( $item );
		}
	}
}
