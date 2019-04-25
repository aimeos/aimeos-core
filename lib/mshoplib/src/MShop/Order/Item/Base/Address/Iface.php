<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Address;


/**
 * Interface for order address items.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\Position\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId();

	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setAddressId( $addrid );

	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId();

	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param string $value New base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setBaseId( $value );

	/**
	 * Copys all data from a given address.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address New address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $address );
}
