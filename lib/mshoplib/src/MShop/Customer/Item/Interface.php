<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Customer
 */


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
interface MShop_Customer_Item_Interface
	extends MShop_Common_Item_ListRef_Interface
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
	 * @return void
	 */
	public function setLabel( $value );

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
	 * @return void
	 */
	public function setStatus( $value );

	/**
	 * Returns the unique code of the customer item.
	 * This should be the username or the e-mail address.
	 *
	 * @return string Unique code of the customer item
	 */
	public function getCode();

	/**
	 * Sets the code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 * @return void
	 */
	public function setCode( $value );

	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string Birthday date of the customer (YYYY-MM-DD format)
	 */
	public function getBirthday();

	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param date $value Birthday of the customer item (YYYY-MM-DD format)
	 * @return void
	 */
	public function setBirthday( $value );

	/**
	 * Returns the billing address of the customer item.
	 *
	 * @return MShop_Common_Item_Address_Interface Address object
	 */
	public function getPaymentAddress();

	/**
	 * Sets the billing address of the customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address Billing address of the customer item
	 * @return void
	 */
	public function setPaymentAddress( MShop_Common_Item_Address_Interface $address );

	/**
	 * Returns the password of the customer item.
	 *
	 * @return string Encrypted password
	 */
	public function getPassword();

	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value Password of the customer item
	 * @return void
	 */
	public function setPassword( $value );

	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified();

	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD format) or null if unknown
	 * @return void
	 */
	public function setDateVerified( $value );

	/**
	 * Returns the group IDs the customer belongs to
	 *
	 * @return array List of group IDs
	 */
	public function getGroups();
}
