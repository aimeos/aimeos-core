<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Supplier
 */


/**
 * Interface for provider supplier address DTO objects used by the shop.
 * @package MShop
 * @subpackage Supplier
 */
interface MShop_Supplier_Item_Address_Iface extends MShop_Common_Item_Address_Iface
{
	/**
	 * Returns the reference id regarding to the product suppliercode of the address.
	 *
	 * @return string Address reference id
	 */
	public function getRefId();


	/**
	 * Sets the new reference id regarding to the product suppliercode of the address.
	 *
	 * @param string $refid New reference id of the address
	 * @return void
	 */
	public function setRefId( $refid );


	/**
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition();


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position New position of the address item
	 * @return void
	 */
	public function setPosition( $position );


	/**
	 * Copies the values of the order address item into the address item.
	 *
	 * @param MShop_Order_Item_Base_Address_Iface $item Order address item
	 * @return MShop_Common_Item_Address_Iface The address item for method chaining
	 */
	public function copyFrom( MShop_Order_Item_Base_Address_Iface $item );

}
