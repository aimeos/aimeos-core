<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for all order base manager implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Manager_Base_Interface
	extends MShop_Common_Manager_Factory_Interface
{
	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return MShop_Order_Item_Base_Interface Shopping basket
	 */
	public function getSession( $type = '' );

	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return integer Lock status (@see MShop_Order_Manager_Base_Base)
	 */
	public function getSessionLock( $type = '' );

	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 */
	public function setSession( MShop_Order_Item_Base_Interface $order, $type = '' );

	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param integer $lock Lock value (@see MShop_Order_Manager_Base_Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws MShop_Order_Exception if the lock value is invalid
	 */
	public function setSessionLock( $lock, $type = '' );

	/**
	 * Creates a new basket containing the items from the order excluding the coupons.
	 * If the last parameter is ture, the items will be marked as new and
	 * modified so an additional order is stored when the basket is saved.
	 *
	 * @param integer $baseId Base ID of the order to load
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @param boolean $fresh Create a new basket by copying the existing one and remove IDs
	 * @return MShop_Order_Item_Base_Interface Basket including all items
	 */
	public function load( $baseId, $parts = MShop_Order_Manager_Base_Base::PARTS_ALL, $fresh = false );

	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object containing all information
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	public function store( MShop_Order_Item_Base_Interface $basket, $parts = MShop_Order_Manager_Base_Base::PARTS_ALL );
}
