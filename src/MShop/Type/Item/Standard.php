<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 * @package MShop
 * @subpackage Type
 */


namespace Aimeos\MShop\Type\Item;


/**
 * Default implementation of the type item
 *
 * @package MShop
 * @subpackage Type
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Type\Item\Iface
{
	/**
	 * Returns the code of the type item
	 *
	 * @return string Code of the type item
	 */
	public function getCode() : string
	{
		return $this->get( $this->prefix() . 'code', '' );
	}


	/**
	 * Sets the code of the type item
	 *
	 * @param string $code New code of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'code', \Aimeos\Utils::code( $code ) );
	}


	/**
	 * Returns the domain of the type item
	 *
	 * @return string Domain of the type item
	 */
	public function getDomain() : string
	{
		return $this->get( $this->prefix() . 'domain', '' );
	}


	/**
	 * Sets the domain of the type item
	 *
	 * @param string $domain New domain of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'domain', $domain );
	}


	/**
	 * Returns the translations of the type item label
	 *
	 * @return array Translations of the type item label
	 */
	public function getI18n() : array
	{
		return (array) $this->get( $this->prefix() . 'i18n', [] );
	}


	/**
	 * Sets the translations of the type item label
	 *
	 * @param array $value New translations of the type item label
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setI18n( array $value ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'i18n', $value );
	}


	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName() : string
	{
		return $this->getI18n()[$this->get( '.language' )] ?? $this->getLabel();
	}


	/**
	 * Returns the label of the type item
	 *
	 * @return string Label of the type item
	 */
	public function getLabel() : string
	{
		return $this->get( $this->prefix() . 'label', '' );
	}


	/**
	 * Sets the label of the type item
	 *
	 * @param string $label New label of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'label', $label );
	}


	/**
	 * Returns the position of the item in the list.
	 *
	 * @return int Position of the item in the list
	 */
	public function getPosition() : int
	{
		return $this->get( $this->prefix() . 'position', 0 );
	}


	/**
	 * Sets the new position of the item in the list.
	 *
	 * @param int $pos position of the item in the list
	 * @return \Aimeos\MShop\Type\Item\Iface Item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'position', $pos );
	}


	/**
	 * Returns the status of the type item
	 *
	 * @return int Status of the type item
	 */
	public function getStatus() : int
	{
		return $this->get( $this->prefix() . 'status', 1 );
	}


	/**
	 * Sets the status of the type item
	 *
	 * @param int $status New status of the type item
	 * @return \Aimeos\MShop\Type\Item\Iface Common type item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Type\Item\Iface
	{
		return $this->set( $this->prefix() . 'status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return str_replace( '.', '/', trim( $this->prefix(), '.' ) );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Type\Item\Iface Type item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Type\Item\Iface
	{
		$prefix = $this->prefix();
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $prefix . 'code': $item->setCode( $value ); break;
				case $prefix . 'domain': $item->setDomain( $value ); break;
				case $prefix . 'i18n': $item->setI18n( (array) $value ); break;
				case $prefix . 'label': $item->setLabel( $value ); break;
				case $prefix . 'position': $item->setPosition( (int) $value ); break;
				case $prefix . 'status': $item->setStatus( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns an associative list of item properties.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array List of item properties.
	 */
	public function toArray( bool $private = false ) : array
	{
		$prefix = $this->prefix();
		$list = parent::toArray( $private );

		$list[$prefix . 'code'] = $this->getCode();
		$list[$prefix . 'domain'] = $this->getDomain();
		$list[$prefix . 'label'] = $this->getLabel();
		$list[$prefix . 'position'] = $this->getPosition();
		$list[$prefix . 'status'] = $this->getStatus();
		$list[$prefix . 'i18n'] = $this->getI18n();
		$list[$prefix . 'name'] = $this->getName();

		return $list;
	}
}
