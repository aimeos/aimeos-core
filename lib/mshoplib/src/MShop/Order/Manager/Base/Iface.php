<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Shopping basket
	 */
	public function getSession( string $type = 'default' ) : \Aimeos\MShop\Order\Item\Base\Iface;

	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return int Lock status (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 */
	public function getSessionLock( string $type = 'default' ) : int;

	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Base\Iface $order, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Base\Iface;

	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param int $lock Lock value (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function setSessionLock( int $lock, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Base\Iface;

	/**
	 * Creates a new basket containing the items from the order excluding the coupons.
	 * If the last parameter is ture, the items will be marked as new and
	 * modified so an additional order is stored when the basket is saved.
	 *
	 * @param string $baseId Base ID of the order to load
	 * @param int $parts Bitmap of the basket parts that should be loaded
	 * @param bool $fresh Create a new basket by copying the existing one and remove IDs
	 * @param bool $default True to use default criteria, false for no limitation
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Basket including all items
	 */
	public function load( string $baseId, int $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, bool $fresh = false,
		bool $default = false ) : \Aimeos\MShop\Order\Item\Base\Iface;

	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object containing all information
	 * @param int $parts Bitmap of the basket parts that should be stored
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Stored order basket
	 */
	public function store( \Aimeos\MShop\Order\Item\Base\Iface $basket,
		int $parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL ) : \Aimeos\MShop\Order\Item\Base\Iface;
}
