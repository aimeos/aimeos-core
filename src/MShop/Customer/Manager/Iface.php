<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Verifies the password against the stored password hash of the customer
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Stored customer item
	 * @param string $password Plain text password entered by the user
	 * @return bool TRUE if the password matches the stored one, FALSE if not
	 */
	public function verify( \Aimeos\MShop\Customer\Item\Iface $item, string $password ) : bool;
}
