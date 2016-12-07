<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item;


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\ListRef\Iface
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
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
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
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
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
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setCode( $value );

	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string|null Birthday date of the customer (YYYY-MM-DD format)
	 */
	public function getBirthday();

	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param string|null $value Birthday of the customer item (YYYY-MM-DD format)
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setBirthday( $value );

	/**
	 * Returns the billing address of the customer item.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Address object
	 */
	public function getPaymentAddress();

	/**
	 * Sets the billing address of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billing address of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address );

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
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
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
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setDateVerified( $value );

	/**
	 * Returns the group IDs the customer belongs to
	 *
	 * @return array List of group IDs
	 */
	public function getGroups();

	/**
	 * Sets the group IDs the customer belongs to
	 *
	 * @param array $ids List of group IDs
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setGroups( array $ids );
}
