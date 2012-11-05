<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Customer_Item_Interface
{
	private $_billingaddress;
	private $_salt;
	private $_values;

	/**
	 * Initializes the customer item object
	 *
	 * @param array $values List of attributes that belong to the customer item
	 */
	public function __construct( MShop_Common_Item_Address_Interface $address, array $values = array(),
		array $listItems = array(), array $refItems = array(), $salt = '' )
	{
		parent::__construct('customer.', $values, $listItems, $refItems);

		$this->_values = $values;
		
		if( isset( $values['salutation'] ) ) {
			$address->setSalutation( (string) $values['salutation'] );
		}
		
		if( isset( $values['company'] ) ) {
			$address->setCompany( (string) $values['company'] );
		}
		
		if( isset( $values['title'] ) ) {
			$address->setTitle( (string) $values['title'] );
		}
		
		if( isset( $values['firstname'] ) ) {
			$address->setFirstname( (string) $values['firstname'] );
		}
		
		if( isset( $values['lastname'] ) ) {
			$address->setLastname( (string) $values['lastname'] );
		}
		
		if( isset( $values['address1'] ) ) {
			$address->setAddress1( (string) $values['address1'] );
		}
		
		if( isset( $values['address2'] ) ) {
			$address->setAddress2( (string) $values['address2'] );
		}
		
		if( isset( $values['address3'] ) ) {
			$address->setAddress3( (string) $values['address3'] );
		}
		
		if( isset( $values['postal'] ) ) {
			$address->setPostal( (string) $values['postal'] );
		}
		
		if( isset( $values['city'] ) ) {
			$address->setCity( (string) $values['city'] );
		}
		
		if( isset( $values['state'] ) ) {
			$address->setState( (string) $values['state'] );
		}
		
		if( isset( $values['langid'] ) ) {
			$address->setLanguageId( (string) $values['langid'] );
		}
		
		if( isset( $values['countryid'] ) ) {
			$address->setCountryId( (string) $values['countryid'] );
		}
		
		if( isset( $values['telephone'] ) ) {
			$address->setTelephone( (string) $values['telephone'] );
		}
		
		if( isset( $values['email'] ) ) {
			$address->setEmail( (string) $values['email'] );
		}
		
		if( isset( $values['telefax'] ) ) {
			$address->setTelefax( (string) $values['telefax'] );
		}
		
		if( isset( $values['website'] ) ) {
			$address->setWebsite( (string) $values['website'] );
		}
		
		$this->_billingaddress = $address;
		$this->_salt = $salt;
	}


	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 */
	public function setLabel( $value )
	{
		if ( $value == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus( $value )
	{
		if ( $value == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the code of the customer item.
	 *
	 * @return string Code of the customer item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 */
	public function setCode( $value )
	{
		if ( $value == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the billingaddress of the customer item.
	 *
	 * @return MShop_Common_Item_Address_Interface
	 */
	public function getBillingAddress()
	{
		return $this->_billingaddress;
	}


	/**
	 * Sets the billingaddress of the customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address Billingaddress of the customer item
	 */
	public function setBillingAddress( MShop_Common_Item_Address_Interface $address )
	{
		if ( $address === $this->_billingaddress ) { return; }

		$this->_billingaddress = $address;
		$this->setModified();
	}


	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string
	 */
	public function getBirthday()
	{
		return ( isset( $this->_values['birthday'] ) ? (string) $this->_values['birthday'] : null );
	}


	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param date $value Birthday of the customer item
	 */
	public function setBirthday( $value )
	{
		if ( $value === $this->getBirthday() ) { return; }

		$this->_checkDateOnlyFormat( $value );

		$this->_values['birthday'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return ( isset( $this->_values['password'] ) ? (string) $this->_values['password'] : '' );
	}


	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 */
	public function setPassword( $value )
	{
		$password = sha1( (string) $value . $this->_salt );
		if ( $password == $this->getPassword() ) { return; }

		$this->_values['password'] = $password;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();
		$list['customer.birthday'] = $this->getBirthday();
		$list['customer.status'] = $this->getStatus();
		$list['customer.password'] = $this->getPassword();
		$list['customer.salutation'] = $this->getBillingAddress()->getSalutation();
		$list['customer.company'] = $this->getBillingAddress()->getCompany();
		$list['customer.title'] = $this->getBillingAddress()->getTitle();
		$list['customer.firstname'] = $this->getBillingAddress()->getFirstname();
		$list['customer.lastname'] = $this->getBillingAddress()->getLastname();
		$list['customer.address1'] = $this->getBillingAddress()->getAddress1();
		$list['customer.address2'] = $this->getBillingAddress()->getAddress2();
		$list['customer.address3'] = $this->getBillingAddress()->getAddress3();
		$list['customer.postal'] = $this->getBillingAddress()->getPostal();
		$list['customer.city'] = $this->getBillingAddress()->getCity();
		$list['customer.state'] = $this->getBillingAddress()->getState();
		$list['customer.languageid'] = $this->getBillingAddress()->getLanguageId();
		$list['customer.countryid'] = $this->getBillingAddress()->getCountryId();
		$list['customer.telephone'] = $this->getBillingAddress()->getTelephone();
		$list['customer.email'] = $this->getBillingAddress()->getEmail();
		$list['customer.telefax'] = $this->getBillingAddress()->getTelefax();
		$list['customer.website'] = $this->getBillingAddress()->getWebsite();
		return $list;
	}


	/**
	 * Implements deep copies for clones.
	 */
	public function __clone()
	{
		$this->_billingaddress = clone $this->_billingaddress;
	}


	/**
	 * Tests if the date param represents an ISO format
	 *
	 * @param string ISO date in yyyy-mm-dd format
	 */
	protected function _checkDateOnlyFormat( $date )
	{
		if( $date !== null && preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $date ) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid date format "%1$s"', $date ) );
		}
	}
}
