<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\AddressRef;
use \Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
abstract class Base
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Customer\Item\Iface
{
	use ListsRef\Traits, PropertyRef\Traits, AddressRef\Traits  {
		ListsRef\Traits::__clone insteadof PropertyRef\Traits;
		ListsRef\Traits::__clone insteadof AddressRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		AddressRef\Traits::__clone as __cloneAddress;
		PropertyRef\Traits::__clone as __cloneProperty;
	}


	private \Aimeos\MShop\Common\Item\Address\Iface $payaddress;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param string $prefix Property prefix for the values
	 * @param array $values List of attributes that belong to the customer item
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->initListItems( $values['.listitems'] ?? [] );
		$this->initAddressItems( $values['.addritems'] ?? [] );
		$this->initPropertyItems( $values['.propitems'] ?? [] );

		$this->payaddress = $address->setId( $this->getId() ); // set modified flag to false
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		$this->payaddress = clone $this->payaddress;

		parent::__clone();
		$this->__cloneList();
		$this->__cloneAddress();
		$this->__cloneProperty();
	}


	/**
	 * Returns the payaddress of the customer item.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function getPaymentAddress() : \Aimeos\MShop\Common\Item\Address\Iface
	{
		return $this->payaddress;
	}


	/**
	 * Sets the payaddress of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billingaddress of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address ) : \Aimeos\MShop\Customer\Item\Iface
	{
		if( $address === $this->payaddress && $address->isModified() === false ) { return $this; }

		$this->payaddress = $address;
		$this->setModified();

		return $this;
	}


	/**
	 * Tests if this item object was modified
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified() : bool
	{
		return parent::isModified() || $this->getPaymentAddress()->isModified();
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );
		$addr = $item->getPaymentAddress()->fromArray( $list, $private );

		return $item->setPaymentAddress( $addr );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['customer.salutation'] = $this->getPaymentAddress()->getSalutation();
		$list['customer.company'] = $this->getPaymentAddress()->getCompany();
		$list['customer.vatid'] = $this->getPaymentAddress()->getVatID();
		$list['customer.title'] = $this->getPaymentAddress()->getTitle();
		$list['customer.firstname'] = $this->getPaymentAddress()->getFirstname();
		$list['customer.lastname'] = $this->getPaymentAddress()->getLastname();
		$list['customer.address1'] = $this->getPaymentAddress()->getAddress1();
		$list['customer.address2'] = $this->getPaymentAddress()->getAddress2();
		$list['customer.address3'] = $this->getPaymentAddress()->getAddress3();
		$list['customer.postal'] = $this->getPaymentAddress()->getPostal();
		$list['customer.city'] = $this->getPaymentAddress()->getCity();
		$list['customer.state'] = $this->getPaymentAddress()->getState();
		$list['customer.languageid'] = $this->getPaymentAddress()->getLanguageId();
		$list['customer.countryid'] = $this->getPaymentAddress()->getCountryId();
		$list['customer.telephone'] = $this->getPaymentAddress()->getTelephone();
		$list['customer.mobile'] = $this->getPaymentAddress()->getMobile();
		$list['customer.email'] = $this->getPaymentAddress()->getEmail();
		$list['customer.telefax'] = $this->getPaymentAddress()->getTelefax();
		$list['customer.website'] = $this->getPaymentAddress()->getWebsite();
		$list['customer.longitude'] = $this->getPaymentAddress()->getLongitude();
		$list['customer.latitude'] = $this->getPaymentAddress()->getLatitude();
		$list['customer.birthday'] = $this->getPaymentAddress()->getBirthday();

		return $list;
	}
}
