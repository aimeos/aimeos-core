<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Property;


/**
 * Default property item implementation.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Property\Iface
{
	private $prefix;
	private $values;


	/**
	 * Initializes the property item object with the given values
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list type item
	 */
	public function __construct( $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = $prefix;
		$this->values = $values;
	}


	/**
	 * Returns the language ID of the property item.
	 *
	 * @return string|null Language ID of the property item
	 */
	public function getLanguageId()
	{
		if( isset( $this->values[$this->prefix . 'languageid'] ) ) {
			return (string) $this->values[$this->prefix . 'languageid'];
		}

		return null;
	}


	/**
	 *  Sets the language ID of the property item.
	 *
	 * @param string|null $id Language ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setLanguageId( $id )
	{
		if( $id !== $this->getLanguageId() )
		{
			$this->values[$this->prefix . 'languageid'] = $this->checkLanguageId( $id );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the parent id of the property item
	 *
	 * @return string|null Parent ID of the property item
	 */
	public function getParentId()
	{
		if( isset( $this->values[$this->prefix . 'parentid'] ) ) {
			return (string) $this->values[$this->prefix . 'parentid'];
		}
	}


	/**
	 * Sets the new parent ID of the property item
	 *
	 * @param string $id Parent ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setParentId( $id )
	{
		if( (string) $id !== $this->getParentId() )
		{
			$this->values[$this->prefix . 'parentid'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type code of the property item.
	 *
	 * @return string|null Type code of the property item
	 */
	public function getType()
	{
		if( isset( $this->values[$this->prefix . 'type'] ) ) {
			return (string) $this->values[$this->prefix . 'type'];
		}
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values[$this->prefix . 'typename'] ) ) {
			return (string) $this->values[$this->prefix . 'typename'];
		}
	}


	/**
	 * Returns the type id of the property item
	 *
	 * @return string|null Type of the property item
	 */
	public function getTypeId()
	{
		if( isset( $this->values[$this->prefix . 'typeid'] ) ) {
			return (string) $this->values[$this->prefix . 'typeid'];
		}
	}


	/**
	 * Sets the new type of the property item
	 *
	 * @param string $id Type of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setTypeId( $id )
	{
		if( (string) $id !== $this->getTypeId() )
		{
			$this->values[$this->prefix . 'typeid'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue()
	{
		if( isset( $this->values[$this->prefix . 'value'] ) ) {
			return (string) $this->values[$this->prefix . 'value'];
		}

		return '';
	}


	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setValue( $value )
	{
		if( (string) $value !== $this->getValue() )
		{
			$this->values[$this->prefix . 'value'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return str_replace( '.', '/', rtrim( $this->prefix, '.' ) );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && ( $this->values['languageid'] === null
			|| $this->getLanguageId() === null
			|| $this->getLanguageId() === $this->values['languageid'] );
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
		unset( $list[$this->prefix . 'type'], $list[$this->prefix . 'typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': $this->setParentId( $value ); break;
				case $this->prefix . 'typeid': $this->setTypeId( $value ); break;
				case $this->prefix . 'languageid': $this->setLanguageId( $value ); break;
				case $this->prefix . 'value': $this->setValue( $value ); break;
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

		$list[$this->prefix . 'typename'] = $this->getTypeName();
		$list[$this->prefix . 'languageid'] = $this->getLanguageId();
		$list[$this->prefix . 'value'] = $this->getValue();
		$list[$this->prefix . 'type'] = $this->getType();

		if( $private === true )
		{
			$list[$this->prefix . 'parentid'] = $this->getParentId();
			$list[$this->prefix . 'typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
