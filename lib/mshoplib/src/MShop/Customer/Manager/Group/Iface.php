<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager\Group;


/**
 * Interface for customer group managers
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Inserts a new or updates an existing customer group item
	 *
	 * @param \Aimeos\MShop\Customer\Item\Group\Iface $item Customer group item
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Customer\Item\Group\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Customer\Item\Group\Iface;
}
