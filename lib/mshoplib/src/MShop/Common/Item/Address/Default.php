<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Address_Default
	extends MShop_Common_Item_Address_Base
	implements MShop_Common_Item_Address_Interface
{
	private $prefix;
	private $values;

	/**
	 * Initializes the provider common address item object
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values List of attributes that belong to the provider common address item
	 */
	public function __construct( $prefix, array $values = array( ) )
	{
		parent::__construct( $prefix, $values );

		$this->values = $values;
		$this->prefix = $prefix;
	}


	/**
	 * Returns the reference id regarding to the product suppliercode of the address.
	 *
	 * @return string Address reference id
	 */
	public function getRefId()
	{
		return ( isset( $this->values['refid'] ) ? (string) $this->values['refid'] : '' );
	}


	/**
	 * Sets the new reference id regarding to the product suppliercode of the address.
	 *
	 * @param string $refid New reference id of the address
	 */
	public function setRefId( $refid )
	{
		if( $refid == $this->getRefId() ) { return; }

		$this->values['refid'] = (string) $refid;
		$this->setModified();
	}


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position Position of the address item
	 */
	public function setPosition( $position )
	{
		if( $position == $this->getPosition() ) { return; }

		$this->values['pos'] = (int) $position;
		$this->setModified();
	}


	/**
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition()
	{
		return ( isset( $this->values['pos'] ) ? (int) $this->values['pos'] : 0 );
	}


	/**
	 * Copies the values of the order address item into the address item.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $item Order address item
	 */
	public function copyFrom( MShop_Order_Item_Base_Address_Interface $item )
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
				case $this->prefix . 'refid': $this->setRefId( $value ); break;
				case $this->prefix . 'position': $this->setPosition( $value ); break;
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
		$properties = parent::toArray();

		$properties[$this->prefix . 'refid'] = $this->getRefId();
		$properties[$this->prefix . 'position'] = $this->getPosition();

		return $properties;
	}

}
