<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId() : string;

	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setAddressId( string $addrid ) : \Aimeos\MShop\Order\Item\Base\Address\Iface;

	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return string|null Base ID
	 */
	public function getBaseId() : ?string;

	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param string|null $value New base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setBaseId( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Address\Iface;

	/**
	 * Returns the position of the address in the order.
	 *
	 * @return integer|null Address position in the order from 0-n
	 */
	public function getPosition() : ?int;

	/**
	 * Sets the position of the address within the list of ordered addresses
	 *
	 * @param int|null $value Address position in the order from 0-n or null for resetting the position
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If the position is invalid
	 */
	public function setPosition( ?int $value ) : \Aimeos\MShop\Order\Item\Base\Address\Iface;

	/**
	 * Copys all data from a given address.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address New address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $address ) : \Aimeos\MShop\Common\Item\Address\Iface;
}
