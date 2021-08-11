<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\AddressRef\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\PropertyRef\Iface,
		\Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setLabel( ?string $value ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Returns the unique code of the customer item.
	 * This should be the username or the e-mail address.
	 *
	 * @return string Unique code of the customer item
	 */
	public function getCode() : string;

	/**
	 * Sets the code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Returns the billing address of the customer item.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Address object
	 */
	public function getPaymentAddress() : \Aimeos\MShop\Common\Item\Address\Iface;

	/**
	 * Sets the billing address of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billing address of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Returns the password of the customer item.
	 *
	 * @return string Encrypted password
	 */
	public function getPassword() : string;

	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value Password of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPassword( string $value ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified() : ?string;

	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD format) or null if unknown
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setDateVerified( ?string $value ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Returns the group IDs the customer belongs to
	 *
	 * @return array List of group IDs
	 */
	public function getGroups() : array;

	/**
	 * Sets the group IDs the customer belongs to
	 *
	 * @param string[] $ids List of group IDs
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setGroups( array $ids ) : \Aimeos\MShop\Customer\Item\Iface;

	/**
	 * Tests if the user is a super user
	 *
	 * @return bool TRUE if user is a super user, FALSE if not
	 */
	public function isSuper() : bool;
}
