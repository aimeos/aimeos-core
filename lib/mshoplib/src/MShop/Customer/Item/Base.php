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
abstract class Base
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Customer\Item\Iface
{
	private $addresses;
	private $billingaddress;
	private $sortedAddr;
	private $values;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addresses List of referenced address items
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = [],
		array $listItems = [], array $refItems = [], $addresses = [] )
	{
		parent::__construct( 'customer.', $values, $listItems, $refItems );

		foreach( $values as $name => $value )
		{
			switch( $name )
			{
				case 'customer.salutation': $address->setSalutation( $value ); break;
				case 'customer.company': $address->setCompany( $value ); break;
				case 'customer.vatid': $address->setVatId( $value ); break;
				case 'customer.title': $address->setTitle( $value ); break;
				case 'customer.firstname': $address->setFirstname( $value ); break;
				case 'customer.lastname': $address->setLastname( $value ); break;
				case 'customer.address1': $address->setAddress1( $value ); break;
				case 'customer.address2': $address->setAddress2( $value ); break;
				case 'customer.address3': $address->setAddress3( $value ); break;
				case 'customer.postal': $address->setPostal( $value ); break;
				case 'customer.city': $address->setCity( $value ); break;
				case 'customer.state': $address->setState( $value ); break;
				case 'customer.languageid': $address->setLanguageId( $value ); break;
				case 'customer.countryid': $address->setCountryId( $value ); break;
				case 'customer.telephone': $address->setTelephone( $value ); break;
				case 'customer.telefax': $address->setTelefax( $value ); break;
				case 'customer.website': $address->setWebsite( $value ); break;
				case 'customer.longitude': $address->setLongitude( $value ); break;
				case 'customer.latitude': $address->setLatitude( $value ); break;
				case 'customer.email': $address->setEmail( $value ); break;
			}
		}

		// set modified flag to false
		$address->setId( $this->getId() );

		$this->billingaddress = $address;
		$this->addresses = $addresses;
		$this->values = $values;
	}


	/**
	 * Returns the delivery address items of the customer
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of IDs as keys and address items as values
	 */
	public function getAddressItems()
	{
		if( $this->sortedAddr === null )
		{
			$fcn = function( $a, $b )
			{
				if( $a->getPosition() == $b->getPosition() ) {
					return 0;
				}

				return ( $a->getPosition() < $b->getPosition() ? -1 : 1 );
			};

			uasort( $this->addresses, $fcn );
			$this->sortedAddr = true;
		}

		return $this->addresses;
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


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );
		$addr = $this->getPaymentAddress();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.salutation': $addr->setSalutation( $value ); break;
				case 'customer.company': $addr->setCompany( $value ); break;
				case 'customer.vatid': $addr->setVatID( $value ); break;
				case 'customer.title': $addr->setTitle( $value ); break;
				case 'customer.firstname': $addr->setFirstname( $value ); break;
				case 'customer.lastname': $addr->setLastname( $value ); break;
				case 'customer.address1': $addr->setAddress1( $value ); break;
				case 'customer.address2': $addr->setAddress2( $value ); break;
				case 'customer.address3': $addr->setAddress3( $value ); break;
				case 'customer.postal': $addr->setPostal( $value ); break;
				case 'customer.city': $addr->setCity( $value ); break;
				case 'customer.state': $addr->setState( $value ); break;
				case 'customer.languageid': $addr->setLanguageId( $value ); break;
				case 'customer.countryid': $addr->setCountryId( $value ); break;
				case 'customer.telephone': $addr->setTelephone( $value ); break;
				case 'customer.email': $addr->setEmail( $value ); break;
				case 'customer.telefax': $addr->setTelefax( $value ); break;
				case 'customer.website': $addr->setWebsite( $value ); break;
				case 'customer.longitude': $addr->setLongitude( $value ); break;
				case 'customer.latitude': $addr->setLatitude( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
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


	/**
	 * Implements deep copies for clones.
	 */
	public function __clone()
	{
		$this->billingaddress = clone $this->billingaddress;
	}
}
