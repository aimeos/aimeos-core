<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Address_Default
	extends MShop_Common_Item_Address_Abstract
	implements MShop_Common_Item_Address_Interface
{
	private $_prefix;
	private $_values;

	/**
	 * Initializes the provider common address item object
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values List of attributes that belong to the provider common address item
	 */
	public function __construct( $prefix, array $values = array( ) )
	{
		parent::__construct($prefix, $values);

		$this->_values = $values;
		$this->_prefix = $prefix;
	}


	/**
	 * Returns the reference id regarding to the product suppliercode of the address.
	 *
	 * @return string Address reference id
	 */
	public function getRefId()
	{
		return ( isset( $this->_values['refid'] ) ? (string) $this->_values['refid'] : '' );
	}


	/**
	 * Sets the new reference id regarding to the product suppliercode of the address.
	 *
	 * @param string $refid New reference id of the address
	 */
	public function setRefId( $refid )
	{
		if ( $refid == $this->getRefId() ) { return; }

		$this->_values['refid'] = (string) $refid;
		$this->setModified();
	}


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position Position of the address item
	 */
	public function setPosition( $position )
	{
		if ( $position == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $position;
		$this->setModified();
	}


	/**
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$properties = parent::toArray();

		$properties[$this->_prefix . 'refid'] = $this->getRefId();
		$properties[$this->_prefix . 'position'] = $this->getPosition();

		return $properties;
	}

}
