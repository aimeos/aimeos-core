<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
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
