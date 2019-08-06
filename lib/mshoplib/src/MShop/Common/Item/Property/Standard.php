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
	 * Returns the unique key of the property item
	 *
	 * @return string Unique key consisting of type/language/value
	 */
	public function getKey()
	{
		return $this->getType() . '|' . ( $this->getLanguageId() ?: 'null' ) . '|' . md5( $this->getValue() );
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
	 * Sets the new type of the property item
	 *
	 * @param string $type Type of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values[$this->prefix . 'type'] = $this->checkCode( $type );
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
		return parent::isAvailable() && ( $this->values['.languageid'] === null
			|| $this->getLanguageId() === $this->values['.languageid']
			|| $this->getLanguageId() === null );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Property item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case $this->prefix . 'languageid': $item = $item->setLanguageId( $value ); break;
				case $this->prefix . 'value': $item = $item->setValue( $value ); break;
				case $this->prefix . 'type': $item = $item->setType( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
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

		$list[$this->prefix . 'languageid'] = $this->getLanguageId();
		$list[$this->prefix . 'value'] = $this->getValue();
		$list[$this->prefix . 'type'] = $this->getType();

		if( $private === true )
		{
			$list[$this->prefix . 'key'] = $this->getKey();
			$list[$this->prefix . 'parentid'] = $this->getParentId();
		}

		return $list;
	}

}
