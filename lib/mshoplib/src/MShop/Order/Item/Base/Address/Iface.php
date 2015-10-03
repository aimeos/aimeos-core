<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for order address items.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Address_Iface extends MShop_Common_Item_Address_Iface
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
	 * @return void
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
	 * @return void
	 */
	public function setType( $type );

	/**
	 * Copys all data from a given address.
	 *
	 * @param MShop_Common_Item_Address_Iface $address New address
	 * @return void
	 */
	public function copyFrom( MShop_Common_Item_Address_Iface $address );

}
