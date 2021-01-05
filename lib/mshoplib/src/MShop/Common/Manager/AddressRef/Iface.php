<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\AddressRef;


/**
 * Interface for all manager implementations using address items
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Creates a new address item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Address\Iface New address item object
	 */
	public function createAddressItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Address\Iface;
}
