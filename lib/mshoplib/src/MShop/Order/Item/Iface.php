<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Interface for all order item implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the order number
	 *
	 * @return string Order number
	 */
	public function getOrderNumber() : string;

	/**
	 * Returns the associated order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface|null Order base item
	 */
	public function getBaseItem() : ?\Aimeos\MShop\Order\Item\Base\Iface;

	/**
	 * Sets the associated order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item
	 */
	public function setBaseItem( \Aimeos\MShop\Order\Item\Base\Iface $baseItem ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the basic order ID.
	 *
	 * @return string|null Basic order ID
	 */
	public function getBaseId() : ?string;

	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param string $id ID of the basic order item
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setBaseId( string $id ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery() : ?string;

	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDateDelivery( ?string $date ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the payment date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment() : ?string;

	/**
	 * Sets the payment date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDatePayment( ?string $date ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return int|null Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusDelivery() : ?int;

	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param int|null $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusDelivery( ?int $status ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return int|null Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusPayment() : ?int;

	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param int|null $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusPayment( ?int $status ) : \Aimeos\MShop\Order\Item\Iface;

	/**
	 * Returns the related invoice ID.
	 *
	 * @return string|null Related invoice ID
	 */
	public function getRelatedId() : ?string;

	/**
	 * Sets the related invoice ID.
	 *
	 * @param string|null $id Related invoice ID
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setRelatedId( ?string $id ) : \Aimeos\MShop\Order\Item\Iface;

}
