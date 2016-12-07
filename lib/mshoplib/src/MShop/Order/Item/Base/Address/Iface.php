<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface extends \Aimeos\MShop\Common\Item\Address\Iface
{
	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return integer|null Base ID
	 */
	public function getBaseId();

	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param integer|null $value New base ID
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setBaseId( $value );

	/**
	 * Returns the type of the address which can be billing or delivery.
	 *
	 * @return string Type of the address
	 */
	public function getType();

	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function setType( $type );

	/**
	 * Copys all data from a given address.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address New address
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Order base address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $address );
}
