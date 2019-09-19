<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Address;


/**
 * Abstract class for address items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Address\Iface
{
	/**
	 * Saluation is not known.
	 * The customer didn't choose a valid salutation.
	 */
	const SALUTATION_UNKNOWN = '';

	/**
	 * Saluation for a company.
	 */
	const SALUTATION_COMPANY = 'company';

	/**
	 * Saluation for a female customer.
	 */
	const SALUTATION_MRS = 'mrs';

	/**
	 * Saluation for a female customer (not maried, usually not used any more).
	 */
	const SALUTATION_MISS = 'miss';

	/**
	 * Saluation for a male customer.
	 */
	const SALUTATION_MR = 'mr';

	private $prefix;


	/**
	 * Initializes the address item.
	 *
	 * @param string $prefix Key prefix that should be used for toArray()/fromArray() like "customer.address."
	 * @param array $values Associative list of key/value pairs containing address data
	 */
	public function __construct( $prefix, array $values )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = $prefix;
	}




	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany()
	{
		return (string) $this->get( $this->prefix . 'company', '' );
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setCompany( $company )
	{
		return $this->set( $this->prefix . 'company', (string) $company );
	}


	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatID()
	{
		return (string) $this->get( $this->prefix . 'vatid', '' );
	}


	/**
	 * Sets a new vatid.
	 *
	 * @param string $vatid New vatid
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setVatID( $vatid )
	{
		return $this->set( $this->prefix . 'vatid', (string) $vatid );
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function getSalutation()
	{
		return (string) $this->get( $this->prefix . 'salutation', \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN );
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setSalutation( $salutation )
	{
		return $this->set( $this->prefix . 'salutation', (string) $this->checkSalutation( $salutation ) );
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle()
	{
		return (string) $this->get( $this->prefix . 'title', '' );
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setTitle( $title )
	{
		return $this->set( $this->prefix . 'title', (string) $title );
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname()
	{
		return (string) $this->get( $this->prefix . 'firstname', '' );
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setFirstname( $firstname )
	{
		return $this->set( $this->prefix . 'firstname', (string) $firstname );
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname()
	{
		return (string) $this->get( $this->prefix . 'lastname', '' );
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setLastname( $lastname )
	{
		return $this->set( $this->prefix . 'lastname', (string) $lastname );
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1()
	{
		return (string) $this->get( $this->prefix . 'address1', '' );
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setAddress1( $address1 )
	{
		return $this->set( $this->prefix . 'address1', (string) $address1 );
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2()
	{
		return (string) $this->get( $this->prefix . 'address2', '' );
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setAddress2( $address2 )
	{
		return $this->set( $this->prefix . 'address2', (string) $address2 );
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3()
	{
		return (string) $this->get( $this->prefix . 'address3', '' );
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setAddress3( $address3 )
	{
		return $this->set( $this->prefix . 'address3', (string) $address3 );
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal()
	{
		return (string) $this->get( $this->prefix . 'postal', '' );
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setPostal( $postal )
	{
		return $this->set( $this->prefix . 'postal', (string) $postal );
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity()
	{
		return (string) $this->get( $this->prefix . 'city', '' );
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setCity( $city )
	{
		return $this->set( $this->prefix . 'city', (string) $city );
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState()
	{
		return (string) $this->get( $this->prefix . 'state', '' );
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setState( $state )
	{
		return $this->set( $this->prefix . 'state', (string) $state );
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string|null Unique ID of the country
	 */
	public function getCountryId()
	{
		return (string) $this->get( $this->prefix . 'countryid' );
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countryid Unique ID of the country
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setCountryId( $countryid )
	{
		return $this->set( $this->prefix . 'countryid', $this->checkCountryId( $countryid ) );
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string|null Unique ID of the language
	 */
	public function getLanguageId()
	{
		return (string) $this->get( $this->prefix . 'languageid' );
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setLanguageId( $langid )
	{
		return $this->set( $this->prefix . 'languageid', $this->checkLanguageId( $langid ) );
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone()
	{
		return (string) $this->get( $this->prefix . 'telephone', '' );
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setTelephone( $telephone )
	{
		return $this->set( $this->prefix . 'telephone', (string) $telephone );
	}


	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail()
	{
		return (string) $this->get( $this->prefix . 'email', '' );
	}


	/**
	 * Sets a new email address.
	 *
	 * @param string $email New email address
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setEmail( $email )
	{
		if( $email != '' && preg_match( '/^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$/', $email ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in email address: "%1$s"', $email ) );
		}

		return $this->set( $this->prefix . 'email', (string) $email );
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax()
	{
		return (string) $this->get( $this->prefix . 'telefax', '' );
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setTelefax( $telefax )
	{
		return $this->set( $this->prefix . 'telefax', (string) $telefax );
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite()
	{
		return (string) $this->get( $this->prefix . 'website', '' );
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string|null $website New website URL
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function setWebsite( $website )
	{
		$pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';

		if( $website != '' && preg_match( $pattern, $website ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid web site URL "%1$s"', $website ) );
		}

		return $this->set( $this->prefix . 'website', (string) $website );
	}


	/**
	 * Returns the longitude coordinate of the customer address
	 *
	 * @return float|null Longitude coordinate as decimal value or null
	 */
	public function getLongitude()
	{
		return (string) $this->get( $this->prefix . 'longitude' );
	}


	/**
	 * Sets the longitude coordinate of the customer address
	 *
	 * @param float|null $value Longitude coordinate as decimal value or null
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setLongitude( $value )
	{
		return $this->set( $this->prefix . 'longitude', $value !== '' && $value !== null ? (float) $value : null );
	}


	/**
	 * Returns the latitude coordinate of the customer address
	 *
	 * @return float|null Latitude coordinate as decimal value or null
	 */
	public function getLatitude()
	{
		return (string) $this->get( $this->prefix . 'latitude' );
	}


	/**
	 * Sets the latitude coordinate of the customer address
	 *
	 * @param float|null $value Latitude coordinate as decimal value or null
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setLatitude( $value )
	{
		return $this->set( $this->prefix . 'latitude', $value !== '' && $value !== null ? (float) $value : null );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return str_replace( '.', '/', rtrim( $this->prefix, '.' ) );
	}


	/**
	 * Copies the values of the address item into another one.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Address item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Common address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $item )
	{
		$this->setCompany( $item->getCompany() );
		$this->setVatID( $item->getVatID() );
		$this->setSalutation( $item->getSalutation() );
		$this->setTitle( $item->getTitle() );
		$this->setFirstname( $item->getFirstname() );
		$this->setLastname( $item->getLastname() );
		$this->setAddress1( $item->getAddress1() );
		$this->setAddress2( $item->getAddress2() );
		$this->setAddress3( $item->getAddress3() );
		$this->setPostal( $item->getPostal() );
		$this->setCity( $item->getCity() );
		$this->setState( $item->getState() );
		$this->setCountryId( $item->getCountryId() );
		$this->setLanguageId( $item->getLanguageId() );
		$this->setTelephone( $item->getTelephone() );
		$this->setTelefax( $item->getTelefax() );
		$this->setEmail( $item->getEmail() );
		$this->setWebsite( $item->getWebsite() );
		$this->setLongitude( $item->getLongitude() );
		$this->setLatitude( $item->getLatitude() );

		$this->setModified();

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Address\Iface Address item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			if( strncmp( 'customer.address.', $key, 17 ) !== 0 ) {
				$key = str_replace( ['order.base.address.', 'customer.'], $this->prefix, $key );
			} else {
				$key = str_replace( 'customer.address.', $this->prefix, $key );
			}

			switch( $key )
			{
				case $this->prefix . 'salutation': $item = $item->setSalutation( $value ); break;
				case $this->prefix . 'company': $item = $item->setCompany( $value ); break;
				case $this->prefix . 'vatid': $item = $item->setVatID( $value ); break;
				case $this->prefix . 'title': $item = $item->setTitle( $value ); break;
				case $this->prefix . 'firstname': $item = $item->setFirstname( $value ); break;
				case $this->prefix . 'lastname': $item = $item->setLastname( $value ); break;
				case $this->prefix . 'address1': $item = $item->setAddress1( $value ); break;
				case $this->prefix . 'address2': $item = $item->setAddress2( $value ); break;
				case $this->prefix . 'address3': $item = $item->setAddress3( $value ); break;
				case $this->prefix . 'postal': $item = $item->setPostal( $value ); break;
				case $this->prefix . 'city': $item = $item->setCity( $value ); break;
				case $this->prefix . 'state': $item = $item->setState( $value ); break;
				case $this->prefix . 'countryid': $item = $item->setCountryId( $value ); break;
				case $this->prefix . 'languageid': $item = $item->setLanguageId( $value ); break;
				case $this->prefix . 'telephone': $item = $item->setTelephone( $value ); break;
				case $this->prefix . 'telefax': $item = $item->setTelefax( $value ); break;
				case $this->prefix . 'email': $item = $item->setEmail( $value ); break;
				case $this->prefix . 'website': $item = $item->setWebsite( $value ); break;
				case $this->prefix . 'longitude': $item = $item->setLongitude( $value ); break;
				case $this->prefix . 'latitude': $item = $item->setLatitude( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
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

		$list[$this->prefix . 'salutation'] = $this->getSalutation();
		$list[$this->prefix . 'company'] = $this->getCompany();
		$list[$this->prefix . 'vatid'] = $this->getVatID();
		$list[$this->prefix . 'title'] = $this->getTitle();
		$list[$this->prefix . 'firstname'] = $this->getFirstname();
		$list[$this->prefix . 'lastname'] = $this->getLastname();
		$list[$this->prefix . 'address1'] = $this->getAddress1();
		$list[$this->prefix . 'address2'] = $this->getAddress2();
		$list[$this->prefix . 'address3'] = $this->getAddress3();
		$list[$this->prefix . 'postal'] = $this->getPostal();
		$list[$this->prefix . 'city'] = $this->getCity();
		$list[$this->prefix . 'state'] = $this->getState();
		$list[$this->prefix . 'countryid'] = $this->getCountryId();
		$list[$this->prefix . 'languageid'] = $this->getLanguageId();
		$list[$this->prefix . 'telephone'] = $this->getTelephone();
		$list[$this->prefix . 'telefax'] = $this->getTelefax();
		$list[$this->prefix . 'email'] = $this->getEmail();
		$list[$this->prefix . 'website'] = $this->getWebsite();
		$list[$this->prefix . 'longitude'] = $this->getLongitude();
		$list[$this->prefix . 'latitude'] = $this->getLatitude();

		return $list;
	}


	/**
	 * Checks the given address salutation is valid
	 *
	 * @param string $value Address salutation defined in \Aimeos\MShop\Common\Item\Address\Base
	 * @throws \Aimeos\MShop\Exception If salutation is invalid
	 */
	protected function checkSalutation( $value )
	{
		switch( $value )
		{
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR:
				return $value;
		}

		throw new \Aimeos\MShop\Exception( sprintf( 'Address salutation "%1$s" not within allowed range', $value ) );
	}
}
