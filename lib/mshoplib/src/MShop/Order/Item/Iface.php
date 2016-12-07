<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the basic order ID.
	 *
	 * @return integer Basic order ID
	 */
	public function getBaseId();

	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param integer $id ID of the basic order item
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setBaseId( $id );

	/**
	 * Returns the type of the invoice (repeating, web, phone, etc).
	 *
	 * @return integer Invoice type
	 */
	public function getType();

	/**
	 * Sets the type of the invoice.
	 *
	 * @param integer $type Invoice type
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setType( $type );

	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery();

	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDateDelivery( $date );

	/**
	 * Returns the payment date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment();

	/**
	 * Sets the payment date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDatePayment( $date );

	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getDeliveryStatus();

	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDeliveryStatus( $status );

	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getPaymentStatus();

	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setPaymentStatus( $status );

	/**
	 * Returns the related invoice ID.
	 *
	 * @return integer|null Related invoice ID
	 */
	public function getRelatedId();

	/**
	 * Sets the related invoice ID.
	 *
	 * @param integer|null Related invoice ID
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setRelatedId( $id );

}
