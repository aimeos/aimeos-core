<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Customer
 */
interface Iface extends \Aimeos\MShop\Common\Item\Address\Iface
{
	/**
	 * Returns the type of the address item.
	 *
	 * @return string Address type
	 */
	public function getType() : string;

	/**
	 * Sets the type of the address item.
	 *
	 * @param string $type Address type
	 * @return \Aimeos\MShop\Customer\Item\Address\Iface Address item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Customer\Item\Address\Iface;
}
