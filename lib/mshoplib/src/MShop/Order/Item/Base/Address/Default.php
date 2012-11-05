<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
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
	 * Returns the base Id.
	 *
	 * @return integer Base Id
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the Base Id.
	 *
	 * @param integer $baseid New base Id
	 */
	public function setBaseId( $baseid )
	{
		if ( $baseid == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = (int) $baseid;
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
		$this->setCompany( $address->getCompany() );
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
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['order.base.address.baseid'] = $this->getBaseId();
		$list['order.base.address.type'] = $this->getType();

		return $list;
	}

}
