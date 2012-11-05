<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
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
	 * @return integer Lock status (@see MShop_Order_Manager_Base_Abstract)
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
	 * @param integer $lock Lock value (@see MShop_Order_Manager_Base_Abstract)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws MShop_Order_Exception if the lock value is invalid
	 */
	public function setSessionLock( $lock, $type = '' );

	/**
	 * Creates a new basket containing all items from the order excluding the coupons.
	 * The items will be marked as new and modified so an additional order is
	 * stored when the basket is saved.
	 *
	 * @param integer $baseId Base ID of the order to load
	 * @return MShop_Order_Item_Base_Interface Basket including all items
	 */
	public function load( $baseId );

	/**
	 * Saves the complete basket to the storage including all items attached.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object containing all information
	 */
	public function store( MShop_Order_Item_Base_Interface $basket );
}
