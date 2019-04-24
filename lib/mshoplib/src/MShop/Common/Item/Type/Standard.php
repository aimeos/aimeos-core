<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	private $values;


	/**
	 * Initializes the type item object.
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
	 * Returns the code of the common list type item
	 *
	 * @return string Code of the common list type item
	 */
	public function getCode()
	{
		if( isset( $this->values[$this->prefix . 'code'] ) ) {
			return (string) $this->values[$this->prefix . 'code'];
		}

		return '';
	}


	/**
	 * Sets the code of the common list type item
	 *
	 * @param string $code New code of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( (string) $code !== $this->getCode() )
		{
			$this->values[$this->prefix . 'code'] = $this->checkCode( $code );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the domain of the common list type item
	 *
	 * @return string Domain of the common list type item
	 */
	public function getDomain()
	{
		if( isset( $this->values[$this->prefix . 'domain'] ) ) {
			return (string) $this->values[$this->prefix . 'domain'];
		}

		return '';
	}


	/**
	 * Sets the domain of the common list type item
	 *
	 * @param string $domain New domain of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( (string) $domain !== $this->getDomain() )
		{
			$this->values[$this->prefix . 'domain'] = (string) $domain;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the translated name for the type item
	 *
	 * @return string Translated name of the type item
	 */
	public function getName()
	{
		if( isset( $this->values[$this->prefix . 'name'] ) ) {
			return (string) $this->values[$this->prefix . 'name'];
		}

		return $this->getLabel();
	}


	/**
	 * Returns the label of the common list type item
	 *
	 * @return string Label of the common list type item
	 */
	public function getLabel()
	{
		if( isset( $this->values[$this->prefix . 'label'] ) ) {
			return (string) $this->values[$this->prefix . 'label'];
		}

		return '';
	}


	/**
	 * Sets the label of the common list type item
	 *
	 * @param string $label New label of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values[$this->prefix . 'label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}

	/**
	 * Returns the position of the item in the list.
	 *
	 * @return integer Position of the item in the list
	 */
	public function getPosition()
	{
		if( isset( $this->values[$this->prefix . 'position'] ) ) {
			return (int) $this->values[$this->prefix . 'position'];
		}

		return 0;
	}


	/**
	 * Sets the new position of the item in the list.
	 *
	 * @param integer $pos position of the item in the list
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		if( (int) $pos !== $this->getPosition() )
		{
			$this->values[$this->prefix . 'position'] = (int) $pos;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the common list type item
	 *
	 * @return integer Status of the common list type item
	 */
	public function getStatus()
	{
		if( isset( $this->values[$this->prefix . 'status'] ) ) {
			return (int) $this->values[$this->prefix . 'status'];
		}

		return 1;
	}


	/**
	 * Sets the status of the common list type item
	 *
	 * @param integer $status New status of the common list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Common type item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values[$this->prefix . 'status'] = (int) $status;
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
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Type item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'code': $item = $item->setCode( $value ); break;
				case $this->prefix . 'domain': $item = $item->setDomain( $value ); break;
				case $this->prefix . 'label': $item = $item->setLabel( $value ); break;
				case $this->prefix . 'position': $item = $item->setPosition( $value ); break;
				case $this->prefix . 'status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns an associative list of item properties.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array List of item properties.
	 */
	public function toArray( $private = false )
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
