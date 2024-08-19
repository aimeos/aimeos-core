<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Generic interface for order manager implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a new address item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Address\Iface New order address item object
	 */
	public function createAddress( array $values = [] ) : \Aimeos\MShop\Order\Item\Address\Iface;

	/**
	 * Creates a new coupon item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Coupon\Iface New order coupon item object
	 */
	public function createCoupon( array $values = [] ) : \Aimeos\MShop\Order\Item\Coupon\Iface;

	/**
	 * Creates a new product item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Iface New order product item object
	 */
	public function createProduct( array $values = [] ) : \Aimeos\MShop\Order\Item\Product\Iface;

	/**
	 * Creates a new product attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface New order product attribute item object
	 */
	public function createProductAttribute( array $values = [] ) : \Aimeos\MShop\Order\Item\Product\Attribute\Iface;

	/**
	 * Creates a new service item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Iface New order service item object
	 */
	public function createService( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Iface;

	/**
	 * Creates a new service attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface New order service attribute item object
	 */
	public function createServiceAttribute( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Attribute\Iface;

	/**
	 * Creates a new service transaction item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
	 */
	public function createServiceTransaction( array $values = [] ) : \Aimeos\MShop\Order\Item\Service\Transaction\Iface;

	/**
	 * Creates a new status item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Status\Iface New order item object
	 */
	public function createStatus( array $values = [] ) : \Aimeos\MShop\Order\Item\Status\Iface;

	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Iface Shopping basket
	 */
	public function getSession( string $type = 'default' ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Iface $order, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Iface;
}
