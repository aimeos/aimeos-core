<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Provides basic methods
 */
class BaseAddTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Returns the list of task names which depends on this task
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return ['CatalogRebuildTestIndex'];
	}


	/**
	 * Adds product test data
	 */
	public function migrate()
	{
	}


	/**
	 * Adds the property test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $addrManager Address manager object
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item object
	 * @param array $data Associative list of key/list pairs
	 * @return \Aimeos\MShop\Common\Item\AddressRef\Iface Modified item object
	 */
	protected function addAddressData( \Aimeos\MShop\Common\Manager\Iface $addrManager, \Aimeos\MShop\Common\Item\AddressRef\Iface $item, array $data )
	{
		if( isset( $data['address'] ) )
		{
			foreach( $data['address'] as $entry ) {
				$item->addAddressItem( $addrManager->createItem( $entry )->fromArray( $entry ) );
			}
		}

		return $item;
	}


	/**
	 * Adds the list test data
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $listManager List manager object
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item object
	 * @param array $data List of key/list pairs lists
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Modified item object
	 */
	protected function addListData( \Aimeos\MShop\Common\Manager\Iface $listManager, \Aimeos\MShop\Common\Item\ListRef\Iface $item, array $data )
	{
		if( isset( $data['lists'] ) )
		{
			foreach( $data['lists'] as $domain => $entries )
			{
				$manager = $this->getManager( $domain );
				$refItems = $this->getRefItems( $domain );

				foreach( $entries as $entry )
				{
					$listItem = $listManager->createItem()->fromArray( $entry );

					if( isset( $entry['ref'] ) && isset( $refItems[$entry['ref']] ) ) {
						$refItem = $refItems[$entry['ref']];
					} else {
						$refItem = $manager->createItem()->fromArray( $entry );
					}

					if( $refItem instanceof \Aimeos\MShop\Common\Item\ListRef\Iface ) {
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
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Modified item object
	 */
	protected function addPropertyData( \Aimeos\MShop\Common\Manager\Iface $propManager, \Aimeos\MShop\Common\Item\PropertyRef\Iface $item, array $data )
	{
		if( isset( $data['property'] ) )
		{
			foreach( $data['property'] as $entry ) {
				$item->addPropertyItem( $propManager->createItem()->fromArray( $entry ) );
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
	protected function getManager( $domain )
	{
		return \Aimeos\MShop::create( $this->additional, $domain );
	}


	/**
	 * Returns the items from the given domains
	 *
	 * @param array $domain Domain names
	 * @return array Associative list of label/item pairs
	 */
	protected function getRefItems( $domain )
	{
		$list = [];

		$manager = $this->getManager( $domain );
		$search = $manager->createSearch()->setSlice( 0, 10000 );

		foreach( $manager->searchItems( $search ) as $item ) {
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

				foreach( $testdata[$domain] as $entry ) {
					$manager->saveItem( $manager->createItem()->fromArray( $entry ), false );
				}
			}
		}
	}
}
