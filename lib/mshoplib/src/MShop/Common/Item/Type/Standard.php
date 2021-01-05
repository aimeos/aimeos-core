<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Type;


/**
 * Default implementation of the list item.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Type\Iface
{
	private $prefix;


	/**
	 * Initializes the type item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Initial values of the list type item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = $prefix;
	}


	/**
	 * Returns the code of the common list type item
	 *
	 * @return string Code of the common list type item
	 */
	public function getCode() : string
	{
		return $this->get( $this->prefix . 'code', '' );
	}


	/**
	 * Sets the code of the common list type item
	 *
	 * @param string $code New code of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain() : string
	{
		return $this->get( $this->prefix . 'domain', '' );
	}


	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'domain', $domain );
	}


	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName() : string
	{
		return $this->get( $this->prefix . 'name', $this->getLabel() );
	}


	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel() : string
	{
		return $this->get( $this->prefix . 'label', '' );
	}


	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Common\Item\Type\Iface
	{
		return $this->set( $this->prefix . 'label', $label );
	}


	/**
	 * Returns the position of the item in the list.
	 *
	 * @return int Position of the item in the list
	 */
	public function getPosition() : int
	{
		return $this->get( $this->prefix . 'position', 0 );
	}


	/**
	 * Sets the new position of the item in the list.
	 *
	 * @param int $pos position of the item in the list
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'position', $pos );
	}


	/**
	 * Returns the status of the common list type item
	 *
	 * @return int Status of the common list type item
	 */
	public function getStatus() : int
	{
		return $this->get( $this->prefix . 'status', 1 );
	}


	/**
	 * Sets the status of the common list type item
	 *
	 * @param int $status New status of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return str_replace( '.', '/', rtrim( $this->prefix, '.' ) );
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
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Type item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'code': $item = $item->setCode( $value ); break;
				case $this->prefix . 'domain': $item = $item->setDomain( $value ); break;
				case $this->prefix . 'label': $item = $item->setLabel( $value ); break;
				case $this->prefix . 'position': $item = $item->setPosition( (int) $value ); break;
				case $this->prefix . 'status': $item = $item->setStatus( (int) $value ); break;
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
		$list = parent::toArray( $private );

		$list[$this->prefix . 'code'] = $this->getCode();
		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'name'] = $this->getName();
		$list[$this->prefix . 'label'] = $this->getLabel();
		$list[$this->prefix . 'position'] = $this->getPosition();
		$list[$this->prefix . 'status'] = $this->getStatus();

		return $list;
	}
}
