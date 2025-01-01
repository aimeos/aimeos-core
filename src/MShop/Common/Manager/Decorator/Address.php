<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2025
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Provides a decorator for fetching site items
 *
 * @package MShop
 * @subpackage Common
 */
class Address
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
{
	use \Aimeos\MShop\Common\Manager\AddressRef\Traits;


	/**
	 * Creates objects from the given array
	 *
	 * @param iterable $entries List of associative arrays with key/value pairs
	 * @param array $refs List of domains to retrieve list items and referenced items for
	 * @param array $excludes List of keys which shouldn't be used when creating the items
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function from( iterable $entries, array $refs = [], array $excludes = [] ) : \Aimeos\Map
	{
		$keys = array_flip( $excludes );
		$excludes[] = 'address';

		$items = $this->getManager()->from( $entries, $refs, $excludes );

		foreach( $entries as $key => $entry )
		{
			if( isset( $entry['address'] ) && ( $item = $items->get( $key ) ) )
			{
				foreach( $entry['address'] as $list )
				{
					$list = array_diff_key( $list, $keys );
					$item->addAddressItem( $this->createAddressItem()->fromArray( $list, true ) );
				}
			}
		}

		return $items;
	}


	/**
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->getManager()->saveRefs( $item, $fetch );
		$this->saveAddressItems( $item, $this->domain(), $fetch );

		return $item;
	}


	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		$domain = $this->domain();
		$entries = $this->getManager()->searchRefs( $entries, $ref );

		if( $this->hasRef( $ref, $domain . '/address' ) )
		{
			foreach( $this->getAddressItems( array_keys( $entries ), $domain ) as $id => $list ) {
				$entries[$id]['.addritems'] = $list;
			}
		}

		return $entries;
	}
}
