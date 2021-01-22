<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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


	private $billingaddress;
	private $data;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addrItems List of referenced address items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = [],
		array $listItems = [], array $refItems = [], $addrItems = [], array $propItems = [] )
	{
		parent::__construct( 'customer.', $values );

		$this->initAddressItems( $addrItems );
		$this->initPropertyItems( $propItems );
		$this->initListItems( $listItems, $refItems );

		// set modified flag to false
		$address->setId( $this->getId() );

		$this->billingaddress = $address;
		$this->data = $values;
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		$this->billingaddress = clone $this->billingaddress;

		parent::__clone();
		$this->__cloneList();
		$this->__cloneAddress();
		$this->__cloneProperty();
	}


	/**
	 * Returns the billingaddress of the customer item.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function getPaymentAddress() : \Aimeos\MShop\Common\Item\Address\Iface
	{
		return $this->billingaddress;
	}


	/**
	 * Sets the billingaddress of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billingaddress of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address ) : \Aimeos\MShop\Customer\Item\Iface
	{
		if( $address === $this->billingaddress && $address->isModified() === false ) { return $this; }

		$this->billingaddress = $address;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'customer';
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
		$list['customer.email'] = $this->getPaymentAddress()->getEmail();
		$list['customer.telefax'] = $this->getPaymentAddress()->getTelefax();
		$list['customer.website'] = $this->getPaymentAddress()->getWebsite();
		$list['customer.longitude'] = $this->getPaymentAddress()->getLongitude();
		$list['customer.latitude'] = $this->getPaymentAddress()->getLatitude();
		$list['customer.birthday'] = $this->getPaymentAddress()->getBirthday();

		return $list;
	}
}
