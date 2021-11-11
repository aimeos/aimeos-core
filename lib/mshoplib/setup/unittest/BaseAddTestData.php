<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Provides basic methods
 */
class BaseAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds product test data
	 */
	public function up()
	{
	}


	/**
	 * Adds the property test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $addrManager Address manager object
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item object
	 * @param array $data Associative list of key/list pairs
	 * @return \Aimeos\MShop\Common\Item\Iface Modified item object
	 */
	protected function addAddressData( \Aimeos\MShop\Common\Manager\Iface $addrManager, \Aimeos\MShop\Common\Item\AddressRef\Iface $item, array $data )
	{
		if( isset( $data['address'] ) )
		{
			foreach( $data['address'] as $entry ) {
				$item->addAddressItem( $addrManager->create()->fromArray( $entry ) );
			}
		}

		return $item;
	}


	/**
	 * Adds the list test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $listManager List manager object
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Common\Item\Iface Modified item object
	 */
	protected function addListData( \Aimeos\MShop\Common\Manager\Iface $listManager, \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $data )
	{
		if( isset( $data['lists'] ) )
		{
			foreach( $data['lists'] as $domain => $entries )
			{
				$manager = $this->getManager( $domain );
				$refItems = $this->getRefItems( $domain );

				foreach( $entries as $entry )
				{
					$listItem = $listManager->create()->fromArray( $entry, true );

					if( isset( $entry['ref'] ) && isset( $refItems[$entry['ref']] ) ) {
						$refItem = $refItems[$entry['ref']];
					} else {
						$refItem = $manager->create()->fromArray( $entry, true );
					}

					if( $refItem instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface ) {
						$refItem = $this->addListData( $manager->getSubManager( 'lists' ), $refItem, $entry );
					}

					if( $refItem instanceof \Aimeos\MShop\Common\Item\PropertyRef\Iface ) {
						$refItem = $this->addPropertyData( $manager->getSubManager( 'property' ), $refItem, $entry );
					}

					$item->addListItem( $domain, $listItem, $refItem );
				}
			}
		}

		return $item;
	}


	/**
	 * Adds the property test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $propManager Property manager object
	 * @param \Aimeos\MShop\Common\Item\PropertyRef\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Common\Item\Iface Modified item object
	 */
	protected function addPropertyData( \Aimeos\MShop\Common\Manager\Iface $propManager, \Aimeos\MShop\Common\Item\PropertyRef\Iface $item, array $data )
	{
		if( isset( $data['property'] ) )
		{
			foreach( $data['property'] as $entry ) {
				$item->addPropertyItem( $propManager->create()->fromArray( $entry ) );
			}
		}

		return $item;
	}


	/**
	 * Returns the manager for the current setup task
	 *
	 * @param string $domain Domain name of the manager
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager( string $domain ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return \Aimeos\MShop::create( $this->context(), $domain );
	}


	/**
	 * Returns the items from the given domains
	 *
	 * @param string $domain Domain name
	 * @return array Associative list of label/item pairs
	 */
	protected function getRefItems( string $domain ) : array
	{
		$list = [];

		$manager = $this->getManager( $domain );
		$search = $manager->filter()->slice( 0, 10000 );

		foreach( $manager->search( $search, ['attribute', 'media', 'price', 'product', 'product/property', 'tag', 'text'] ) as $item ) {
			$list[$item->getLabel()] = $item;
		}

		return $list;
	}


	/**
	 * Creates the type test data
	 *
	 * @param array $data List of key/list pairs lists
	 * @param array $domains List of domain names
	 */
	protected function storeTypes( array $testdata, array $domains )
	{
		foreach( $domains as $domain )
		{
			if( isset( $testdata[$domain] ) )
			{
				$subnames = explode( '/', $domain );
				$manager = $this->getManager( array_shift( $subnames ) );

				foreach( $subnames as $subname ) {
					$manager = $manager->getSubManager( $subname );
				}

				foreach( $testdata[$domain] as $entry )
				{
					try {
						$manager->save( $manager->create()->fromArray( $entry ), false );
					} catch( \Exception $e ) {} // Duplicate entry
				}
			}
		}
	}
}
