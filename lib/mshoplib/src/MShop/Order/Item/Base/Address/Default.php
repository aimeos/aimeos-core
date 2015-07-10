<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Default order address container object
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Address_Default
	extends MShop_Order_Item_Base_Address_Abstract
	implements MShop_Order_Item_Base_Address_Interface
{
	private $_values;

	/**
	 * Initializes the objects with the given array of values.
	 *
	 * @param array $values List of address elements
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'order.base.address.', $values );

		$this->_values = $values;
	}


	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return integer|null Base ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param integer|null $value New base ID
	 */
	public function setBaseId( $value )
	{
		if ( $value == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId()
	{
		return ( isset( $this->_values['addrid'] ) ? (string) $this->_values['addrid'] : '' );
	}


	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 */
	public function setAddressId( $addrid )
	{
		if ( $addrid == $this->getAddressId() ) { return; }

		$this->_values['addrid'] = (string) $addrid;
		$this->setModified();
	}


	/**
	 * Returns the address type which can be billing or delivery.
	 *
	 * @return string Address type
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
	}


	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 */
	public function setType( $type )
	{
		if ( $type == $this->getType() ) { return; }

		$this->_checkType( $type );

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Copys all data from a given address.
	 *
	 * @param MShop_Common_Item_Address_Interface $address New address
	 */
	public function copyFrom( MShop_Common_Item_Address_Interface $address )
	{
		$this->setAddressId( $address->getId() );
		$this->setCompany( $address->getCompany() );
		$this->setVatID( $address->getVatID() );
		$this->setSalutation( $address->getSalutation() );
		$this->setTitle( $address->getTitle() );
		$this->setFirstname( $address->getFirstname() );
		$this->setLastname( $address->getLastname() );
		$this->setAddress1( $address->getAddress1() );
		$this->setAddress2( $address->getAddress2() );
		$this->setAddress3( $address->getAddress3() );
		$this->setPostal( $address->getPostal() );
		$this->setCity( $address->getCity() );
		$this->setState( $address->getState() );
		$this->setCountryId( $address->getCountryId() );
		$this->setTelephone( $address->getTelephone() );
		$this->setEmail( $address->getEmail() );
		$this->setTelefax( $address->getTelefax() );
		$this->setWebsite( $address->getWebsite() );
		$this->setLanguageId( $address->getLanguageId() );
		$this->setFlag( $address->getFlag() );

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
				case 'order.base.address.baseid': $this->setBaseId( $value ); break;
				case 'order.base.address.addressid': $this->setAddressId( $value ); break;
				case 'order.base.address.type': $this->setType( $value ); break;
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

		$list['order.base.address.baseid'] = $this->getBaseId();
		$list['order.base.address.addressid'] = $this->getAddressId();
		$list['order.base.address.type'] = $this->getType();

		return $list;
	}

}
