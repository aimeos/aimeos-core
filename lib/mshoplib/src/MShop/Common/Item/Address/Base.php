<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
abstract class Base extends \Aimeos\MShop\Common\Item\Base
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
	private $values;


	/**
	 * Initializes the address item.
	 *
	 * @param string $prefix Key prefix that should be used for toArray()/fromArray() like "customer.address."
	 * @param array $values Associative list of key/value pairs containing address data
	 */
	public function __construct( $prefix, array $values )
	{
		parent::__construct( $prefix, $values );

		$this->values = $values;
		$this->prefix = $prefix;
	}




	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany()
	{
		return ( isset( $this->values['company'] ) ? (string) $this->values['company'] : '' );
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 */
	public function setCompany( $company )
	{
		if( $company == $this->getCompany() ) { return; }

		$this->values['company'] = (string) $company;
		$this->setModified();
	}

	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatID()
	{
		return ( isset( $this->values['vatid'] ) ? (string) $this->values['vatid'] : '' );
	}


	/**
	 * Sets a new vatid.
	 *
	 * @param string $vatid New vatid
	 */
	public function setVatID( $vatid )
	{
		if( $vatid == $this->getVatID() ) { return; }

		$this->values['vatid'] = (string) $vatid;
		$this->setModified();
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function getSalutation()
	{
		return ( isset( $this->values['salutation'] ) ? (string) $this->values['salutation'] : \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN );
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function setSalutation( $salutation )
	{
		if( $salutation == $this->getSalutation() ) { return; }

		$this->checkSalutation( $salutation );

		$this->values['salutation'] = (string) $salutation;
		$this->setModified();
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle()
	{
		return ( isset( $this->values['title'] ) ? (string) $this->values['title'] : '' );
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 */
	public function setTitle( $title )
	{
		if( $title == $this->getTitle() ) { return; }

		$this->values['title'] = (string) $title;
		$this->setModified();
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname()
	{
		return ( isset( $this->values['firstname'] ) ? (string) $this->values['firstname'] : '' );
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 */
	public function setFirstname( $firstname )
	{
		if( $firstname == $this->getFirstname() ) { return; }

		$this->values['firstname'] = (string) $firstname;
		$this->setModified();
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname()
	{
		return ( isset( $this->values['lastname'] ) ? (string) $this->values['lastname'] : '' );
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 */
	public function setLastname( $lastname )
	{
		if( $lastname == $this->getLastname() ) { return; }

		$this->values['lastname'] = (string) $lastname;
		$this->setModified();
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1()
	{
		return ( isset( $this->values['address1'] ) ? (string) $this->values['address1'] : '' );
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 */
	public function setAddress1( $address1 )
	{
		if( $address1 == $this->getAddress1() ) { return; }

		$this->values['address1'] = (string) $address1;
		$this->setModified();
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2()
	{
		return ( isset( $this->values['address2'] ) ? (string) $this->values['address2'] : '' );
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 */
	public function setAddress2( $address2 )
	{
		if( $address2 == $this->getAddress2() ) { return; }

		$this->values['address2'] = (string) $address2;
		$this->setModified();
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3()
	{
		return ( isset( $this->values['address3'] ) ? (string) $this->values['address3'] : '' );
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 */
	public function setAddress3( $address3 )
	{
		if( $address3 == $this->getAddress3() ) { return; }

		$this->values['address3'] = (string) $address3;
		$this->setModified();
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal()
	{
		return ( isset( $this->values['postal'] ) ? (string) $this->values['postal'] : '' );
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 */
	public function setPostal( $postal )
	{
		if( $postal == $this->getPostal() ) { return; }

		$this->values['postal'] = (string) $postal;
		$this->setModified();
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity()
	{
		return ( isset( $this->values['city'] ) ? (string) $this->values['city'] : '' );
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 */
	public function setCity( $city )
	{
		if( $city == $this->getCity() ) { return; }

		$this->values['city'] = (string) $city;
		$this->setModified();
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState()
	{
		return ( isset( $this->values['state'] ) ? (string) $this->values['state'] : '' );
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 */
	public function setState( $state )
	{
		if( $state == $this->getState() ) { return; }

		$this->values['state'] = (string) $state;
		$this->setModified();
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countryid Unique ID of the country
	 */
	public function setCountryId( $countryid )
	{
		if( $countryid === $this->getCountryId() ) { return; }

		$this->values['countryid'] = strtoupper( (string) $countryid );
		$this->setModified();
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId()
	{
		return ( isset( $this->values['countryid'] ) ? (string) $this->values['countryid'] : null );
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 */
	public function setLanguageId( $langid )
	{
		if( $langid === $this->getLanguageId() ) { return; }

		$this->values['langid'] = strtolower( (string) $langid );
		$this->setModified();
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string Unique ID of the language
	 */
	public function getLanguageId()
	{
		return ( isset( $this->values['langid'] ) ? (string) $this->values['langid'] : null );
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone()
	{
		return ( isset( $this->values['telephone'] ) ? (string) $this->values['telephone'] : '' );
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 */
	public function setTelephone( $telephone )
	{
		if( $telephone == $this->getTelephone() ) { return; }

		$this->values['telephone'] = (string) $telephone;
		$this->setModified();
	}


	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail()
	{
		return ( isset( $this->values['email'] ) ? (string) $this->values['email'] : '' );
	}


	/**
	 * Sets a new email address.
	 *
	 * @param string $email New email address
	 */
	public function setEmail( $email )
	{
		if( $email == $this->getEmail() ) { return; }

		if( $email !== '' && preg_match( '/^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$/', $email ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in email address: "%1$s"', $email ) );
		}

		$this->values['email'] = (string) $email;
		$this->setModified();
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax()
	{
		return ( isset( $this->values['telefax'] ) ? (string) $this->values['telefax'] : '' );
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 */
	public function setTelefax( $telefax )
	{
		if( $telefax == $this->getTelefax() ) { return; }

		$this->values['telefax'] = (string) $telefax;
		$this->setModified();
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite()
	{
		return ( isset( $this->values['website'] ) ? (string) $this->values['website'] : '' );
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 */
	public function setWebsite( $website )
	{
		if( $website == $this->getWebsite() ) { return; }

		$pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';

		if( $website !== '' && preg_match( $pattern, $website ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid web site URL "%1$s"', $website ) );
		}

		$this->values['website'] = (string) $website;
		$this->setModified();
	}


	/**
	 * Returns the flag value.
	 *
	 * @return integer Generic flag value
	 */
	public function getFlag()
	{
		return ( isset( $this->values['flag'] ) ? (int) $this->values['flag'] : 0 );
	}


	/**
	 * Sets a new flag value.
	 *
	 * @param integer $flag New flag value
	 */
	public function setFlag( $flag )
	{
		if( $flag == $this->getFlag() ) { return; }

		$this->values['flag'] = (int) $flag;
		$this->setModified();
	}


	/**
	 * Copies the values of the address item into another one.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Address item
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
		$this->setFlag( $item->getFlag() );

		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'salutation': $this->setSalutation( $value ); break;
				case $this->prefix . 'company': $this->setCompany( $value ); break;
				case $this->prefix . 'vatid': $this->setVatID( $value ); break;
				case $this->prefix . 'title': $this->setTitle( $value ); break;
				case $this->prefix . 'firstname': $this->setFirstname( $value ); break;
				case $this->prefix . 'lastname': $this->setLastname( $value ); break;
				case $this->prefix . 'address1': $this->setAddress1( $value ); break;
				case $this->prefix . 'address2': $this->setAddress2( $value ); break;
				case $this->prefix . 'address3': $this->setAddress3( $value ); break;
				case $this->prefix . 'postal': $this->setPostal( $value ); break;
				case $this->prefix . 'city': $this->setCity( $value ); break;
				case $this->prefix . 'state': $this->setState( $value ); break;
				case $this->prefix . 'countryid': $this->setCountryId( $value ); break;
				case $this->prefix . 'languageid': $this->setLanguageId( $value ); break;
				case $this->prefix . 'telephone': $this->setTelephone( $value ); break;
				case $this->prefix . 'telefax': $this->setTelefax( $value ); break;
				case $this->prefix . 'email': $this->setEmail( $value ); break;
				case $this->prefix . 'website': $this->setWebsite( $value ); break;
				case $this->prefix . 'flag': $this->setFlag( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

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
		$list[$this->prefix . 'flag'] = $this->getFlag();

		return $list;
	}


	/**
	 * Checks the given address salutation is valid
	 *
	 * @param integer $value Address salutation defined in \Aimeos\MShop\Common\Item\Address\Base
	 * @throws \Aimeos\MShop\Common\Exception If salutation is invalid
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
				return;
			default:
				throw new \Aimeos\MShop\Common\Exception( sprintf( 'Address salutation "%1$s" not within allowed range', $value ) );
		}
	}
}
