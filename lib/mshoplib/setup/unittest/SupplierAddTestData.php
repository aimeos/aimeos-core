<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds supplier test data and all items from other domains.
 */
class SupplierAddTestData extends \Aimeos\MW\Setup\Task\BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies() : array
	{
		return ['MShopSetLocale', 'ProductAddTestData'];
	}


	/**
	 * Adds supplier test data.
	 */
	public function migrate()
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Context\Item\Iface::class, $this->additional );

		$this->msg( 'Adding supplier test data', 0 );

		$this->additional->setEditor( 'core:lib/mshoplib' );
		$this->process( $this->getData() );

		$this->status( 'done' );
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
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
		}

		return $testdata;
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @param string $domain Domain name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( $domain )
	{
		if( $domain === 'supplier' ) {
			return \Aimeos\MShop\Supplier\Manager\Factory::create( $this->additional, 'Standard' );
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
