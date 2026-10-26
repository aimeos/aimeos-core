<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	private string $prefix;


	/**
	 * Initializes the address item.
	 *
	 * @param string $prefix Key prefix that should be used for toArray()/fromArray() like "customer.address."
	 * @param array $values Associative list of key/value pairs containing address data
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values, str_replace( '.', '/', rtrim( $prefix, '.' ) ) );

		$this->prefix = $prefix;
	}


	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany() : string
	{
		return (string) $this->get( $this->prefix . 'company', '' );
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string|null $company New company name
	 * @return static Common address item for chaining method calls
	 */
	public function setCompany( ?string $company ) : static
	{
		return $this->set( $this->prefix . 'company', (string) $company );
	}


	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatID() : string
	{
		return (string) $this->get( $this->prefix . 'vatid', '' );
	}


	/**
	 * Sets a new vatid.
	 *
	 * @param string|null $vatid New vatid
	 * @return static Common address item for chaining method calls
	 */
	public function setVatID( ?string $vatid ) : static
	{
		return $this->set( $this->prefix . 'vatid', str_replace( ' ', '', (string) $vatid ) );
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation code
	 */
	public function getSalutation() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'salutation', '' );
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string|null $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 * @return static Common address item for chaining method calls
	 */
	public function setSalutation( ?string $salutation ) : static
	{
		return $this->set( $this->prefix . 'salutation', $this->checkSalutation( (string) $salutation ) );
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'title', '' );
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string|null $title New title of the person
	 * @return static Common address item for chaining method calls
	 */
	public function setTitle( ?string $title ) : static
	{
		return $this->set( $this->prefix . 'title', (string) $title );
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'firstname', '' );
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string|null $firstname New first name of the person
	 * @return static Common address item for chaining method calls
	 */
	public function setFirstname( ?string $firstname ) : static
	{
		return $this->set( $this->prefix . 'firstname', (string) $firstname );
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'lastname', '' );
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string|null $lastname New last name of the person
	 * @return static Common address item for chaining method calls
	 */
	public function setLastname( ?string $lastname ) : static
	{
		return $this->set( $this->prefix . 'lastname', (string) $lastname );
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'address1', '' );
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string|null $address1 New first address part
	 * @return static Common address item for chaining method calls
	 */
	public function setAddress1( ?string $address1 ) : static
	{
		return $this->set( $this->prefix . 'address1', (string) $address1 );
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'address2', '' );
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string|null $address2 New second address part
	 * @return static Common address item for chaining method calls
	 */
	public function setAddress2( ?string $address2 ) : static
	{
		return $this->set( $this->prefix . 'address2', (string) $address2 );
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'address3', '' );
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string|null $address3 New third address part
	 * @return static Common address item for chaining method calls
	 */
	public function setAddress3( ?string $address3 ) : static
	{
		return $this->set( $this->prefix . 'address3', (string) $address3 );
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'postal', '' );
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string|null $postal New postal code
	 * @return static Common address item for chaining method calls
	 */
	public function setPostal( ?string $postal ) : static
	{
		return $this->set( $this->prefix . 'postal', (string) $postal );
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'city', '' );
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string|null $city New city name
	 * @return static Common address item for chaining method calls
	 */
	public function setCity( ?string $city ) : static
	{
		return $this->set( $this->prefix . 'city', (string) $city );
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'state', '' );
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string|null $state New state name
	 * @return static Common address item for chaining method calls
	 */
	public function setState( ?string $state ) : static
	{
		return $this->set( $this->prefix . 'state', (string) $state );
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string|null Unique ID of the country
	 */
	public function getCountryId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( $this->prefix . 'countryid' );
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string|null $countryid Unique ID of the country
	 * @return static Common address item for chaining method calls
	 */
	public function setCountryId( ?string $countryid ) : static
	{
		return $this->set( $this->prefix . 'countryid', \Aimeos\Utils::country( $countryid ) );
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string|null Unique ID of the language
	 */
	public function getLanguageId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( $this->prefix . 'languageid' );
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string|null $langid Unique ID of the language
	 * @return static Common address item for chaining method calls
	 */
	public function setLanguageId( ?string $langid ) : static
	{
		return $this->set( $this->prefix . 'languageid', \Aimeos\Utils::language( $langid ) );
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'telephone', '' );
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string|null $telephone New telephone number
	 * @return static Common address item for chaining method calls
	 */
	public function setTelephone( ?string $telephone ) : static
	{
		return $this->set( $this->prefix . 'telephone', (string) $telephone );
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'telefax', '' );
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string|null $telefax New telefax number
	 * @return static Common address item for chaining method calls
	 */
	public function setTelefax( ?string $telefax ) : static
	{
		return $this->set( $this->prefix . 'telefax', (string) $telefax );
	}


	/**
	 * Returns the mobile number.
	 *
	 * @return string Mobile number
	 */
	public function getMobile() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'mobile', '' );
	}


	/**
	 * Sets a new mobile number.
	 *
	 * @param string|null $value New mobile number
	 * @return static Common address item for chaining method calls
	 */
	public function setMobile( ?string $value ) : static
	{
		return $this->set( $this->prefix . 'mobile', (string) $value );
	}


	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'email', '' );
	}


	/**
	 * Sets a new email address.
	 *
	 * @param string|null $email New email address
	 * @return static Common address item for chaining method calls
	 */
	public function setEmail( ?string $email ) : static
	{
		$email = (string) $email;
		$regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

		if( $email != '' && preg_match( $regex, $email ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in email address: "%1$s"', $email ) );
		}

		return $this->set( $this->prefix . 'email', $email );
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'website', '' );
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string|null $website New website URL
	 * @return static Common address item for chaining method calls
	 */
	public function setWebsite( ?string $website ) : static
	{
		$website = (string) $website;
		$pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';

		if( $website != '' && preg_match( $pattern, $website ) !== 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid web site URL "%1$s"', $website ) );
		}

		return $this->set( $this->prefix . 'website', $website );
	}


	/**
	 * Returns the longitude coordinate of the customer address
	 *
	 * @return float|null Longitude coordinate as decimal value or null
	 */
	public function getLongitude() : ?float
	{
		if( ( $result = $this->get( $this->prefix . 'longitude' ) ) !== null ) {
			return (float) $result;
		}

		return null;
	}


	/**
	 * Sets the longitude coordinate of the customer address
	 *
	 * @param string|null $value Longitude coordinate as decimal value or null
	 * @return static Customer address item for chaining method calls
	 */
	public function setLongitude( ?string $value ) : static
	{
		return $this->set( $this->prefix . 'longitude', $value !== '' && $value !== null ? $value : null );
	}


	/**
	 * Returns the latitude coordinate of the customer address
	 *
	 * @return float|null Latitude coordinate as decimal value or null
	 */
	public function getLatitude() : ?float
	{
		if( ( $result = $this->get( $this->prefix . 'latitude' ) ) !== null ) {
			return (float) $result;
		}

		return null;
	}


	/**
	 * Sets the latitude coordinate of the customer address
	 *
	 * @param string|null $value Latitude coordinate as decimal value or null
	 * @return static Customer address item for chaining method calls
	 */
	public function setLatitude( ?string $value ) : static
	{
		return $this->set( $this->prefix . 'latitude', $value !== '' && $value !== null ? $value : null );
	}


	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string|null Birthday in YYYY-MM-DD format
	 */
	public function getBirthday() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( $this->prefix . 'birthday' );
	}


	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param string|null $value Birthday of the customer item
	 * @return static Customer address item for chaining method calls
	 */
	public function setBirthday( ?string $value ) : static
	{
		return $this->set( $this->prefix . 'birthday', \Aimeos\Utils::date( $value ) );
	}


	/**
	 * Returns the customer ID this address belongs to
	 *
	 * @return string|null Customer ID of the address
	 */
	public function getParentId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( $this->prefix . 'parentid' );
	}


	/**
	 * Sets the new customer ID this address belongs to
	 *
	 * @param string|null $parentid New customer ID of the address
	 * @return static Common address item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : static
	{
		return $this->set( $this->prefix . 'parentid', $parentid );
	}


	/**
	 * Returns the position of the address item.
	 *
	 * @return int Position of the address item
	 */
	public function getPosition() : int
	{
		// @phpstan-ignore return.type
		return (int) $this->get( $this->prefix . 'position', 0 );
	}


	/**
	 * Sets the Position of the address item.
	 *
	 * @param int $position Position of the address item
	 * @return static Common address item for chaining method calls
	 */
	public function setPosition( int $position ) : static
	{
		return $this->set( $this->prefix . 'position', $position );
	}


	/**
	 * Returns the type of the address item.
	 * Overwritten for different default value.
	 *
	 * @return string Address type
	 */
	public function getType() : string
	{
		// @phpstan-ignore return.type
		return (string) $this->get( $this->prefix . 'type', 'delivery' );
	}


	/**
	 * Copies the values of the address item into another one.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Address item
	 * @return static Common address item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $item ) : static
	{
		$values = $item->toArray();
		$this->fromArray( $values );

		$this->setType( $item->getType() );
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
		$this->setMobile( $item->getMobile() );
		$this->setEmail( $item->getEmail() );
		$this->setWebsite( $item->getWebsite() );
		$this->setLongitude( $item->getLongitude() !== null ? (string) $item->getLongitude() : null );
		$this->setLatitude( $item->getLatitude() !== null ? (string) $item->getLatitude() : null );
		$this->setBirthday( $item->getBirthday() );

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Address item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $idx => $value )
		{
			$pos = strrpos( $idx, '.' );
			$key = $pos ? substr( $idx, $pos + 1 ) : $idx;

			switch( $key )
			{
				case 'parentid': !$private ?: $item->setParentId( (string) $value ); break;
				case 'type': $item->setType( (string) $value ); break;
				case 'salutation': $item->setSalutation( (string) $value ); break;
				case 'company': $item->setCompany( (string) $value ); break;
				case 'vatid': $item->setVatID( (string) $value ); break;
				case 'title': $item->setTitle( (string) $value ); break;
				case 'firstname': $item->setFirstname( (string) $value ); break;
				case 'lastname': $item->setLastname( (string) $value ); break;
				case 'address1': $item->setAddress1( (string) $value ); break;
				case 'address2': $item->setAddress2( (string) $value ); break;
				case 'address3': $item->setAddress3( (string) $value ); break;
				case 'postal': $item->setPostal( (string) $value ); break;
				case 'city': $item->setCity( (string) $value ); break;
				case 'state': $item->setState( (string) $value ); break;
				case 'countryid': $item->setCountryId( (string) $value ); break;
				case 'languageid': $item->setLanguageId( (string) $value ); break;
				case 'telephone': $item->setTelephone( (string) $value ); break;
				case 'telefax': $item->setTelefax( (string) $value ); break;
				case 'mobile': $item->setMobile( (string) $value ); break;
				case 'email': $item->setEmail( (string) $value ); break;
				case 'website': $item->setWebsite( (string) $value ); break;
				case 'longitude': $item->setLongitude( $value !== null ? (string) $value : null ); break;
				case 'latitude': $item->setLatitude( $value !== null ? (string) $value : null ); break;
				case 'birthday': $item->setBirthday( $value !== null ? (string) $value : null ); break;
				case 'position': $item->setPosition( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$idx] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list[$this->prefix . 'type'] = $this->getType();
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
		$list[$this->prefix . 'mobile'] = $this->getMobile();
		$list[$this->prefix . 'email'] = $this->getEmail();
		$list[$this->prefix . 'website'] = $this->getWebsite();
		$list[$this->prefix . 'longitude'] = $this->getLongitude();
		$list[$this->prefix . 'latitude'] = $this->getLatitude();
		$list[$this->prefix . 'birthday'] = $this->getBirthday();
		$list[$this->prefix . 'position'] = $this->getPosition();

		if( $private === true ) {
			$list[$this->prefix . 'parentid'] = $this->getParentId();
		}

		return $list;
	}


	/**
	 * Checks the given address salutation is valid
	 *
	 * @param string $value Address salutation defined in \Aimeos\MShop\Common\Item\Address\Base
	 * @return string The validated salutation value
	 * @throws \Aimeos\MShop\Exception If salutation is invalid
	 */
	protected function checkSalutation( string $value ) : string
	{
		if( strlen( $value ) > 8 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Address salutation "%1$s" not within allowed range', $value ) );
		}

		return $value;
	}


	/**
	 * Returns the prefix for toArray() and fromArray() methods.
	 *
	 * @return string Prefix for toArray() and fromArray() methods
	 */
	protected function prefix() : string
	{
		return $this->prefix;
	}
}
