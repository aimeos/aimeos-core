<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for all order item implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Interface extends MShop_Common_Item_Interface
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
	 * @return void
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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setDatePayment( $date );

	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from MShop_Order_Item_Base
	 */
	public function getDeliveryStatus();

	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from MShop_Order_Item_Base
	 * @return void
	 */
	public function setDeliveryStatus( $status );

	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from MShop_Order_Item_Base
	 */
	public function getPaymentStatus();

	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from MShop_Order_Item_Base
	 * @return void
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
	 * @throws MShop_Order_Exception If ID is invalid
	 * @return void
	 */
	public function setRelatedId( $id );

}
