<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2024
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	private ?string $langid;
	private string $prefix;


	/**
	 * Initializes the property item object with the given values
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list type item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values, str_replace( '.', '/', rtrim( $prefix, '.' ) ) );

		$this->langid = $values['.languageid'] ?? null;
		$this->prefix = $prefix;
	}


	/**
	 * Returns the unique key of the property item
	 *
	 * @return string Unique key consisting of type/language/value
	 */
	public function getKey() : string
	{
		return substr( $this->getType() . '|' . ( $this->getLanguageId() ?: 'null' ) . '|' . $this->getValue(), 0, 255 );
	}


	/**
	 * Returns the language ID of the property item.
	 *
	 * @return string|null Language ID of the property item
	 */
	public function getLanguageId() : ?string
	{
		return $this->get( $this->prefix . 'languageid' );
	}


	/**
	 *  Sets the language ID of the property item.
	 *
	 * @param string|null $id Language ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setLanguageId( ?string $id ) : \Aimeos\MShop\Common\Item\Property\Iface
	{
		return $this->set( $this->prefix . 'languageid', $this->checkLanguageId( $id ) );
	}


	/**
	 * Returns the parent id of the property item
	 *
	 * @return string|null Parent ID of the property item
	 */
	public function getParentId() : ?string
	{
		return $this->get( $this->prefix . 'parentid' );
	}


	/**
	 * Sets the new parent ID of the property item
	 *
	 * @param string|null $id Parent ID of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setParentId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'parentid', $id );
	}


	/**
	 * Returns the value of the property item.
	 *
	 * @return string Value of the property item
	 */
	public function getValue() : string
	{
		return $this->get( $this->prefix . 'value', '' );
	}


	/**
	 * Sets the new value of the property item.
	 *
	 * @param string $value Value of the property item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Common property item for chaining method calls
	 */
	public function setValue( ?string $value ) : \Aimeos\MShop\Common\Item\Property\Iface
	{
		return $this->set( $this->prefix . 'value', (string) $value );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && ( $this->langid === null
			|| $this->getLanguageId() === $this->langid
			|| $this->getLanguageId() === null );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Property item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item->setParentId( $value ); break;
				case $this->prefix . 'languageid': $item->setLanguageId( $value ); break;
				case $this->prefix . 'value': $item->setValue( $value ); break;
				case $this->prefix . 'type': $item->setType( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
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
