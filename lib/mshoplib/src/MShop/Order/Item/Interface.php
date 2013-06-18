<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 */
	public function setBaseId( $base );

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
	 */
	public function setDatePayment( $date );

	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from MShop_Order_Item_Abstract
	 */
	public function getDeliveryStatus();

	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from MShop_Order_Item_Abstract
	 */
	public function setDeliveryStatus( $status );

	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from MShop_Order_Item_Abstract
	 */
	public function getPaymentStatus();

	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from MShop_Order_Item_Abstract
	 */
	public function setPaymentStatus( $status );

	/**
	 * Returns the order flag.
	 *
	 * @return integer Binary group of bits for order status
	 */
	public function getFlag();

	/**
	 * Sets the order flag.
	 *
	 * @param integer $flag Binary group of bits for order status
	 */
	public function setFlag( $flag );

	/**
	 * Returns the email flag.
	 *
	 * @return integer Binary group of bits for order status
	 */
	public function getEmailFlag();

	/**
	 * Sets the flag.
	 *
	 * @param integer $flag Binary group of bits for email order status
	 */
	public function setEmailFlag( $flag );

	/**
	 * Returns the related invoice ID.
	 *
	 * @param integer|null $id Related invoice ID
	 */
	public function getRelatedId();

	/**
	 * Sets the related invoice ID.
	 *
	 * @param integer|null Related invoice ID
	 * @throws MShop_Order_Exception If ID is invalid
	 */
	public function setRelatedId( $id );

}
