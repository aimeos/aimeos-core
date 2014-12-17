<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product property item implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Property_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Product_Item_Property_Interface
{
	private $_values;


	/**
	 * Initializes the property item object with the given values
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'product.property.', $values );
		
		$this->_values = $values;
	}


	/**
	 * Returns the language ID of the product property item.
	 *
	 * @return string|null Language ID of the product property item
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 *  Sets the language ID of the product property item.
	 *
	 * @param string|null $id Language ID of the product property item
	 */
	public function setLanguageId( $id )
	{
		if ( $id === $this->getLanguageId() ) { return; }

		$this->_checkLanguageId( $id );
		$this->_values['langid'] = $id;
		$this->setModified();
	}


	/**
	 * Returns the parent id of the product property item
	 *
	 * @return integer|null Parent ID of the product property item
	 */
	public function getParentId()
	{
		return ( isset( $this->_values['parentid'] ) ? (int) $this->_values['parentid'] : null );
	}


	/**
	 * Sets the new parent ID of the product property item
	 *
	 * @param integer $id Parent ID of the product property item
	 */
	public function setParentId( $id )
	{
		$id = (int) $id;
		if ( $id === $this->getParentId() ) { return; }

		$this->_values['parentid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the type id of the product property item
	 *
	 * @return integer|null Type of the product property item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}
	
	
	/**
	 * Sets the new type of the product property item
	 *
	 * @param integer|null $id Type of the product property item
	 */
	public function setTypeId( $id )
	{
		$id = (int) $id;
		if ( $id === $this->getTypeId() ) { return; }
	
		$this->_values['typeid'] = (int) $id;
		$this->setModified();
	}
	

	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? (string) $this->_values['value'] : '' );
	}


	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return void
	 */
	public function setValue( $value )
	{
		if ( $value == $this->getValue() ) { return; }

		$this->_values['value'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the type code of the product property item.
	 *
	 * @return string Type code of the product property item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['product.property.parentid'] = $this->getParentId();
		$list['product.property.typeid'] = $this->getTypeId();
		$list['product.property.languageid'] = $this->getLanguageId();
		$list['product.property.value'] = $this->getValue();
		$list['product.property.type'] = $this->getType();

		return $list;
	}

}
