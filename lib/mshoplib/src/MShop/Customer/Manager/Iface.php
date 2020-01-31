<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager;


/**
 * Interface for customer DAOs used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface,
		\Aimeos\MShop\Common\Manager\AddressRef\Iface, \Aimeos\MShop\Common\Manager\ListRef\Iface,
		\Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
	/**
	 * Saves a customer item object.
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Customer\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Customer\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Customer\Item\Iface;
}
