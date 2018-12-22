<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
abstract class Base
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Customer\Item\Iface
{
	use \Aimeos\MShop\Common\Item\ListRef\Traits {
		__clone as __cloneList;
	}
	use \Aimeos\MShop\Common\Item\PropertyRef\Traits {
		__clone as __cloneProperty;
	}
	use \Aimeos\MShop\Common\Item\AddressRef\Traits {
		__clone as __cloneAddress;
	}


	private $billingaddress;
	private $data;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addresses List of referenced address items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = [],
		array $listItems = [], array $refItems = [], $addresses = [], array $propItems = [] )
	{
		parent::__construct( 'customer.', $values );

		$this->initAddressItems( $addresses );
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
	public function getPaymentAddress()
	{
		return $this->billingaddress;
	}


	/**
	 * Sets the billingaddress of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billingaddress of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address )
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
	public function getResourceType()
	{
		return 'customer';
	}


	/**
	 * Tests if this item object was modified
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified()
	{
		return parent::isModified() || $this->getPaymentAddress()->isModified();
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function fromArray( array &$list )
	{
		$item = parent::fromArray( $list );
		$addr = $item->getPaymentAddress();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.salutation': $addr = $addr->setSalutation( $value ); break;
				case 'customer.company': $addr = $addr->setCompany( $value ); break;
				case 'customer.vatid': $addr = $addr->setVatID( $value ); break;
				case 'customer.title': $addr = $addr->setTitle( $value ); break;
				case 'customer.firstname': $addr = $addr->setFirstname( $value ); break;
				case 'customer.lastname': $addr = $addr->setLastname( $value ); break;
				case 'customer.address1': $addr = $addr->setAddress1( $value ); break;
				case 'customer.address2': $addr = $addr->setAddress2( $value ); break;
				case 'customer.address3': $addr = $addr->setAddress3( $value ); break;
				case 'customer.postal': $addr = $addr->setPostal( $value ); break;
				case 'customer.city': $addr = $addr->setCity( $value ); break;
				case 'customer.state': $addr = $addr->setState( $value ); break;
				case 'customer.languageid': $addr = $addr->setLanguageId( $value ); break;
				case 'customer.countryid': $addr = $addr->setCountryId( $value ); break;
				case 'customer.telephone': $addr = $addr->setTelephone( $value ); break;
				case 'customer.email': $addr = $addr->setEmail( $value ); break;
				case 'customer.telefax': $addr = $addr->setTelefax( $value ); break;
				case 'customer.website': $addr = $addr->setWebsite( $value ); break;
				case 'customer.longitude': $addr = $addr->setLongitude( $value ); break;
				case 'customer.latitude': $addr = $addr->setLatitude( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item->setPaymentAddress( $addr );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
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

		return $list;
	}
}
