<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
interface MShop_Customer_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 */
	public function setLabel($value);

	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the status of the item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus($value);

	/**
	 * Returns the code id of the customer item.
	 *
	 * @return string
	 */
	public function getCode();

	/**
	 * Sets the code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 */
	public function setCode( $value );

	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string
	 */
	public function getBirthday();

	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param date $value Birthday of the customer item
	 */
	public function setBirthday( $value );

	/**
	 * Returns the billing address of the customer item.
	 *
	 * @return MShop_Common_Item_Address_Interface
	 */
	public function getBillingAddress();

	/**
	 * Sets the billing address of the customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address Billing address of the customer item
	 */
	public function setBillingAddress( MShop_Common_Item_Address_Interface $address );

	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword();

	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 */
	public function setPassword( $value );
}
