<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
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
		return ['MShopAddTypeDataDefault', 'MShopAddCodeDataDefault', 'DemoAddProductData'];
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
	 * Insert service data.
	 */
	public function up()
	{
		$this->info( 'Processing supplier demo data', 'v' );

		$context = $this->context();
		$value = $context->getConfig()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$manager = \Aimeos\MShop::create( $context, 'supplier' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'supplier.code', 'demo-' ) );
		$services = $manager->search( $search );

		$manager->delete( $services->toArray() );


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

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry, true );

			$item = $this->addRefItems( $item, $entry );
			$item = $this->addProductRefs( $item, $entry );
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


	/**
	 * Adds the referenced product items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with product section
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item
	 */
	protected function addProductRefs( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entry )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'product' );

		foreach( $entry['product'] ?? [] as $data )
		{
			$listItem = $manager->createListItem()->fromArray( $data );
			$listItem->setRefId( $manager->find( $data['product.code'] )->getId() );

			$item->addListItem( 'product', $listItem );
		}

		return $item;
	}


	/**
	 * Adds the referenced items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item
	 */
	protected function addRefItems( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entry )
	{
		$context = $this->context();
		$domain = $item->getResourceType();
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );

		foreach( ['media', 'price', 'text'] as $refDomain )
		{
			if( isset( $entry[$refDomain] ) )
			{
				$manager = \Aimeos\MShop::create( $context, $refDomain );

				foreach( $entry[$refDomain] as $data )
				{
					$listItem = $listManager->create()->fromArray( $data );
					$refItem = $manager->create()->fromArray( $data );

					if( isset( $data['property'] ) )
					{
						foreach( (array) $data['property'] as $property )
						{
							$propItem = $manager->createPropertyItem()->fromArray( $property );
							$refItem->addPropertyItem( $propItem );
						}
					}

					$item->addListItem( $refDomain, $listItem, $refItem );
				}
			}
		}

		return $item;
	}
}
