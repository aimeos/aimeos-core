<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Attribute
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default attribute item implementation.
 *
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Attribute_Item_Interface
{
	private $_values;

	
	/**
	 * Initializes the attribute item.
	 *
	 * @param array $values Associative array with id, domain, code, and status to initialize the item properties; Optional
	 * @param array List of items implementing MShop_Common_List_Item_Interface
	 * @param array List of items implementing MShop_Text_Item_Interface
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'attribute.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the type id of the attribute.
	 *
	 * @return integer|null Type of the attribute
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param integer|null $typeid Type of the attribute
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 */
	public function setCode( $code )
	{
		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0);
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param integer $pos Position of the attribute item
	 */
	public function setPosition( $pos )
	{
		if ( $pos == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
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

		$list['attribute.domain'] = $this->getDomain();
		$list['attribute.code'] = $this->getCode();
		$list['attribute.status'] = $this->getStatus();
		$list['attribute.typeid'] = $this->getTypeId();
		$list['attribute.type'] = $this->getType();
		$list['attribute.position'] = $this->getPosition();
		$list['attribute.label'] = $this->getLabel();

		return $list;
	}

}
