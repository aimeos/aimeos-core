<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Abstract class for address items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Item_Address_Abstract extends MShop_Common_Item_Abstract
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

	private $_prefix;
	private $_values;


	public function __construct( $prefix, array $values )
	{
		parent::__construct( $prefix, $values );

		$this->_values = $values;
		$this->_prefix = $prefix;
	}




	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany()
	{
		return ( isset( $this->_values['company'] ) ? (string) $this->_values['company'] : '' );
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 */
	public function setCompany($company)
	{
		if ( $company == $this->getCompany() ) { return; }

		$this->_values['company'] = (string) $company;
		$this->setModified();
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in MShop_Common_Item_Address_Abstract
	 */
	public function getSalutation()
	{
		return ( isset( $this->_values['salutation'] ) ? (string) $this->_values['salutation'] : MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN );
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in MShop_Common_Item_Address_Abstract
	 */
	public function setSalutation($salutation)
	{
		if ( $salutation == $this->getSalutation() ) { return; }

		$this->_checkSalutation( $salutation );

		$this->_values['salutation'] = (string) $salutation;
		$this->setModified();
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle()
	{
		return ( isset( $this->_values['title'] ) ? (string) $this->_values['title'] : '' );
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 */
	public function setTitle($title)
	{
		if ( $title == $this->getTitle() ) { return; }

		$this->_values['title'] = (string) $title;
		$this->setModified();
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname()
	{
		return ( isset( $this->_values['firstname'] ) ? (string) $this->_values['firstname'] : '' );
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 */
	public function setFirstname($firstname)
	{
		if ( $firstname == $this->getFirstname() ) { return; }

		$this->_values['firstname'] = (string) $firstname;
		$this->setModified();
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname()
	{
		return ( isset( $this->_values['lastname'] ) ? (string) $this->_values['lastname'] : '' );
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 */
	public function setLastname($lastname)
	{
		if ( $lastname == $this->getLastname() ) { return; }

		$this->_values['lastname'] = (string) $lastname;
		$this->setModified();
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1()
	{
		return ( isset( $this->_values['address1'] ) ? (string) $this->_values['address1'] : '' );
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 */
	public function setAddress1($address1)
	{
		if ( $address1 == $this->getAddress1() ) { return; }

		$this->_values['address1'] = (string) $address1;
		$this->setModified();
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2()
	{
		return ( isset( $this->_values['address2'] ) ? (string) $this->_values['address2'] : '' );
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 */
	public function setAddress2($address2)
	{
		if ( $address2 == $this->getAddress2() ) { return; }

		$this->_values['address2'] = (string) $address2;
		$this->setModified();
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3()
	{
		return ( isset( $this->_values['address3'] ) ? (string) $this->_values['address3'] : '' );
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 */
	public function setAddress3($address3)
	{
		if ( $address3 == $this->getAddress3() ) { return; }

		$this->_values['address3'] = (string) $address3;
		$this->setModified();
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal()
	{
		return ( isset( $this->_values['postal'] ) ? (string) $this->_values['postal'] : '' );
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 */
	public function setPostal($postal)
	{
		if ( $postal == $this->getPostal() ) { return; }

		$this->_values['postal'] = (string) $postal;
		$this->setModified();
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity()
	{
		return ( isset( $this->_values['city'] ) ? (string) $this->_values['city'] : '' );
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 */
	public function setCity($city)
	{
		if ( $city == $this->getCity() ) { return; }

		$this->_values['city'] = (string) $city;
		$this->setModified();
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState()
	{
		return ( isset( $this->_values['state'] ) ? (string) $this->_values['state'] : '' );
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 */
	public function setState($state)
	{
		if ( $state == $this->getState() ) { return; }

		$this->_values['state'] = (string) $state;
		$this->setModified();
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countyid Unique ID of the country
	 */
	public function setCountryId($countryid)
	{
		if ( $countryid === $this->getCountryId() ) { return; }

		$this->_values['countryid'] = strtoupper( (string) $countryid );
		$this->setModified();
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId()
	{
		return ( isset( $this->_values['countryid'] ) ? (string) $this->_values['countryid'] : null );
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 */
	public function setLanguageId($langid)
	{
		if ( $langid === $this->getLanguageId() ) { return; }

		$this->_values['langid'] = strtolower( (string) $langid );
		$this->setModified();
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string Unique ID of the language
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone()
	{
		return ( isset( $this->_values['telephone'] ) ? (string) $this->_values['telephone'] : '' );
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 */
	public function setTelephone($telephone)
	{
		if ( $telephone == $this->getTelephone() ) { return; }

		$this->_values['telephone'] = (string) $telephone;
		$this->setModified();
	}


	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail()
	{
		return ( isset( $this->_values['email'] ) ? (string) $this->_values['email'] : '' );
	}


	/**
	 * Sets a new email address.
	 *
	 * @param string $email New email address
	 */
	public function setEmail($email)
	{
		if ( $email == $this->getEmail() ) { return; }

		if( $email !== '' && preg_match('/^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$/', $email) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in email address: "%1$s"', $email ) );
		}

		$this->_values['email'] = (string) $email;
		$this->setModified();
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax()
	{
		return ( isset( $this->_values['telefax'] ) ? (string) $this->_values['telefax'] : '' );
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 */
	public function setTelefax($telefax)
	{
		if ( $telefax == $this->getTelefax() ) { return; }

		$this->_values['telefax'] = (string) $telefax;
		$this->setModified();
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite()
	{
		return ( isset( $this->_values['website'] ) ? (string) $this->_values['website'] : '' );
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 */
	public function setWebsite($website)
	{
		if ( $website == $this->getWebsite() ) { return; }

		if( $website !== '' && preg_match('#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#', $website) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in web site URL "%1$s"', $website ) );
		}

		$this->_values['website'] = (string) $website;
		$this->setModified();
	}


	/**
	 * Returns the flag value.
	 *
	 * @return integer Generic flag value
	 */
	public function getFlag()
	{
		return ( isset( $this->_values['flag'] ) ? (int) $this->_values['flag'] : 0 );
	}


	/**
	 * Sets a new flag value.
	 *
	 * @param integer $flag New flag value
	 */
	public function setFlag($flag)
	{
		if ( $flag == $this->getFlag() ) { return; }

		$this->_values['flag'] = (int) $flag;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list[$this->_prefix . 'countryid'] = $this->getCountryId();
		$list[$this->_prefix . 'company'] = $this->getCompany();
		$list[$this->_prefix . 'salutation'] = $this->getSalutation();
		$list[$this->_prefix . 'title'] = $this->getTitle();
		$list[$this->_prefix . 'firstname'] = $this->getFirstname();
		$list[$this->_prefix . 'lastname'] = $this->getLastname();
		$list[$this->_prefix . 'address1'] = $this->getAddress1();
		$list[$this->_prefix . 'address2'] = $this->getAddress2();
		$list[$this->_prefix . 'address3'] = $this->getAddress3();
		$list[$this->_prefix . 'postal'] = $this->getPostal();
		$list[$this->_prefix . 'city'] = $this->getCity();
		$list[$this->_prefix . 'state'] = $this->getState();
		$list[$this->_prefix . 'email'] = $this->getEmail();
		$list[$this->_prefix . 'telephone'] = $this->getTelephone();
		$list[$this->_prefix . 'telefax'] = $this->getTelefax();
		$list[$this->_prefix . 'website'] = $this->getWebsite();
		$list[$this->_prefix . 'languageid'] = $this->getLanguageId();
		$list[$this->_prefix . 'flag'] = $this->getFlag();

		return $list;
	}


	/**
	 * Checks the given address salutation is valid
	 *
	 * @param integer $value Address salutation defined in MShop_Common_Item_Address_Abstract
	 * @throws MShop_Common_Exception If salutation is invalid
	 */
	protected function _checkSalutation( $value )
	{
		switch( $value )
		{
			case MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN:
			case MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY:
			case MShop_Common_Item_Address_Abstract::SALUTATION_MRS:
			case MShop_Common_Item_Address_Abstract::SALUTATION_MISS:
			case MShop_Common_Item_Address_Abstract::SALUTATION_MR:
				return;
			default:
				throw new MShop_Common_Exception( sprintf( 'Address salutation "%1$s" not within allowed range', $value ) );
		}
	}
}
