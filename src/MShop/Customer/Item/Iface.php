<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	 * @return static Customer item for chaining method calls
	 */
	public function setLabel( ?string $value ) : static;

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
	 * @return static Customer item for chaining method calls
	 */
	public function setCode( string $value ) : static;

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
	 * @return static Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address ) : static;

	/**
	 * Returns the new password hash of the customer item.
	 *
	 * The stored password hash is write-only and never loaded, so an empty
	 * string is returned unless a new password has been set before.
	 *
	 * @return string New password hash or empty string if unchanged
	 */
	public function getPassword() : string;

	/**
	 * Sets a new password for the customer item.
	 *
	 * @param string $value New password of the customer item, empty string keeps the current one
	 * @return static Customer item for chaining method calls
	 */
	public function setPassword( string $value ) : static;

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
	 * @return static Customer item for chaining method calls
	 */
	public function setDateVerified( ?string $value ) : static;

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
	 * @return static Customer item for chaining method calls
	 */
	public function setGroups( array $ids ) : static;

	/**
	 * Tests if the user is a super user
	 *
	 * @return bool TRUE if user is a super user, FALSE if not
	 */
	public function isSuper() : bool;
}
