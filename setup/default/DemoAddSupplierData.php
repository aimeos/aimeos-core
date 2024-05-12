<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to supplier tables.
 */
class DemoAddSupplierData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Supplier', 'Media', 'Text', 'MShopAddTypeDataDefault', 'MShopAddCodeDataDefault'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function before() : array
	{
		return ['DemoRebuildIndex'];
	}


	/**
	 * Insert supplier data.
	 */
	public function up()
	{
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing supplier demo data', 'vv' );

		$manager = \Aimeos\MShop::create( $context, 'supplier' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'supplier.code', 'demo-' ) );
		$items = $manager->search( $search );

		$manager->delete( $items );


		if( $value === '1' )
		{
			$ds = DIRECTORY_SEPARATOR;
			$path = __DIR__ . $ds . 'data' . $ds . 'demo-supplier.php';

			if( ( $data = include( $path ) ) == false ) {
				throw new \RuntimeException( sprintf( 'No file "%1$s" found for supplier domain', $path ) );
			}

			$this->saveItems( $data );
		}
	}


	/**
	 * Stores the supplier items
	 *
	 * @param array $data List of arrays containing the supplier properties
	 */
	protected function saveItems( array $data )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'supplier' );

		foreach( $data as $idx => $entry )
		{
			$item = $manager->create()->fromArray( $entry, true );

			$item = $this->addRefItems( $item, $entry, $idx );
			$item = $this->addAddressItems( $item, $entry );

			$manager->save( $item );
		}
	}


	/**
	 * Adds the referenced product items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with product section
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item
	 */
	protected function addAddressItems( \Aimeos\MShop\Common\Item\AddressRef\Iface $item, array $entry )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'supplier/address' );

		foreach( $entry['address'] ?? [] as $addr ) {
			$item->addAddressItem( $manager->create()->fromArray( $addr, true ) );
		}

		return $item;
	}
}
