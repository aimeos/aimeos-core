<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base;


/**
 * Interface for all order base manager implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Shopping basket
	 */
	public function getSession( $type = '' );

	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return integer Lock status (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 */
	public function getSessionLock( $type = '' );

	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Base\Iface $order, $type = '' );

	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param integer $lock Lock value (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws \Aimeos\MShop\Order\Exception if the lock value is invalid
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
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket including all items
	 */
	public function load( $baseId, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, $fresh = false );

	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object containing all information
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	public function store( \Aimeos\MShop\Order\Item\Base\Iface $basket, $parts = \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );
}
