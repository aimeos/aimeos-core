<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item\Property;


/**
 * Default product property item implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Product\Item\Property\Iface
{
	private $values;


	/**
	 * Initializes the property item object with the given values
	 *
	 * @param array $values Associative list of product property key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'product.property.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the language ID of the product property item.
	 *
	 * @return string|null Language ID of the product property item
	 */
	public function getLanguageId()
	{
		if( isset( $this->values['product.property.languageid'] ) ) {
			return (string) $this->values['product.property.languageid'];
		}

		return null;
	}


	/**
	 *  Sets the language ID of the product property item.
	 *
	 * @param string|null $id Language ID of the product property item
	 * @return \Aimeos\MShop\Product\Item\Property\Iface Product property item for chaining method calls
	 */
	public function setLanguageId( $id )
	{
		if ( $id == $this->getLanguageId() ) { return $this; }

		$this->values['product.property.languageid'] = $this->checkLanguageId( $id );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the parent id of the product property item
	 *
	 * @return integer|null Parent ID of the product property item
	 */
	public function getParentId()
	{
		if( isset( $this->values['product.property.parentid'] ) ) {
			return (int) $this->values['product.property.parentid'];
		}

		return null;
	}


	/**
	 * Sets the new parent ID of the product property item
	 *
	 * @param integer $id Parent ID of the product property item
	 * @return \Aimeos\MShop\Product\Item\Property\Iface Product property item for chaining method calls
	 */
	public function setParentId( $id )
	{
		if( $id == $this->getParentId() ) { return $this; }

		$this->values['product.property.parentid'] = (int) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type code of the product property item.
	 *
	 * @return string|null Type code of the product property item
	 */
	public function getType()
	{
		if( isset( $this->values['product.property.type'] ) ) {
			return (string) $this->values['product.property.type'];
		}

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values['product.property.typename'] ) ) {
			return (string) $this->values['product.property.typename'];
		}

		return null;
	}


	/**
	 * Returns the type id of the product property item
	 *
	 * @return integer|null Type of the product property item
	 */
	public function getTypeId()
	{
		if( isset( $this->values['product.property.typeid'] ) ) {
			return (int) $this->values['product.property.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type of the product property item
	 *
	 * @param integer|null $id Type of the product property item
	 * @return \Aimeos\MShop\Product\Item\Property\Iface Product property item for chaining method calls
	 */
	public function setTypeId( $id )
	{
		if ( $id == $this->getTypeId() ) { return $this; }

		$this->values['product.property.typeid'] = (int) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue()
	{
		if( isset( $this->values['product.property.value'] ) ) {
			return (string) $this->values['product.property.value'];
		}

		return '';
	}


	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return \Aimeos\MShop\Product\Item\Property\Iface Product property item for chaining method calls
	 */
	public function setValue( $value )
	{
		if ( $value == $this->getValue() ) { return $this; }

		$this->values['product.property.value'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'product/property';
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );
		unset( $list['product.property.type'], $list['product.property.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'product.property.parentid': $this->setParentId( $value ); break;
				case 'product.property.typeid': $this->setTypeId( $value ); break;
				case 'product.property.languageid': $this->setLanguageId( $value ); break;
				case 'product.property.value': $this->setValue( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['product.property.typename'] = $this->getTypeName();
		$list['product.property.languageid'] = $this->getLanguageId();
		$list['product.property.value'] = $this->getValue();
		$list['product.property.type'] = $this->getType();

		if( $private === true )
		{
			$list['product.property.parentid'] = $this->getParentId();
			$list['product.property.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
