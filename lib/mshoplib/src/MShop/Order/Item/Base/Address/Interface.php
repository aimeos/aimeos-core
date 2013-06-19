<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for order address items.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Address_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the base Id.
	 *
	 * @return integer Base Id
	 */
	public function getBaseId();

	/**
	 * Sets the Base Id.
	 *
	 * @param integer $baseid New base Id
	 */
	public function setBaseId( $baseid );

	/**
	 * Returns the type of the address which can be billing or delivery.
	 *
	 * @return string Type of the address
	 */
	public function getType();

	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 */
	public function setType( $type );

	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany();

	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 */
	public function setCompany( $company );

	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in MShop_Order_Item_Base_Address_Abstract
	 */
	public function getSalutation();

	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in MShop_Order_Item_Base_Address_Abstract
	 */
	public function setSalutation( $salutation );

	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle();

	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 */
	public function setTitle( $title );

	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname();

	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 */
	public function setFirstname( $firstname );

	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname();

	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 */
	public function setLastname( $lastname );

	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1();

	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 */
	public function setAddress1( $address1 );

	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2();

	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 */
	public function setAddress2( $address2 );

	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3();

	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 */
	public function setAddress3( $address3 );

	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal();

	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 */
	public function setPostal( $postal );

	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity();

	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 */
	public function setCity( $city );

	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState();

	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 */
	public function setState( $state );

	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countyid Unique ID of the country
	 */
	public function setCountryId( $countryid );

	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId();

	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone();

	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 */
	public function setTelephone( $telephone );

	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail();

	/**
	 * Sets a new email address.
	 *
	 * @param string $email New email address
	 */
	public function setEmail( $email );

	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax();

	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 */
	public function setTelefax( $telefax );

	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite();

	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 */
	public function setWebsite( $website );

	/**
	 * Copys all data from a given address.
	 *
	 * @param MShop_Common_Item_Address_Interface $address New address
	 */
	public function copyFrom( MShop_Common_Item_Address_Interface $address );

}
