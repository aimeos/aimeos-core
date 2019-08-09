<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Item;


/**
 * Default attribute item implementation.
 *
 * @package MShop
 * @subpackage Attribute
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Attribute\Item\Iface
{
	use \Aimeos\MShop\Common\Item\ListRef\Traits {
		__clone as __cloneList;
	}
	use \Aimeos\MShop\Common\Item\PropertyRef\Traits {
		__clone as __cloneProperty;
	}


	private $values;


	/**
	 * Initializes the attribute item.
	 *
	 * @param array $values Associative array with id, domain, code, and status to initialize the item properties; Optional
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( array $values = [], array $listItems = [],
		array $refItems = [], array $propItems = [] )
	{
		parent::__construct( 'attribute.', $values );

		$this->initListItems( $listItems, $refItems );
		$this->initPropertyItems( $propItems );

		$this->values = $values;
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();
		$this->__cloneList();
		$this->__cloneProperty();
	}


	/**
	 * Returns the unique key of the attribute item
	 *
	 * @return string Unique key consisting of domain/type/code
	 */
	public function getKey()
	{
		return $this->getDomain() . '|' . $this->getType() . '|' . $this->getCode();
	}


	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain()
	{
		if( isset( $this->values['attribute.domain'] ) ) {
			return (string) $this->values['attribute.domain'];
		}

		return '';
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( (string) $domain !== $this->getDomain() )
		{
			$this->values['attribute.domain'] = (string) $domain;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType()
	{
		if( isset( $this->values['attribute.type'] ) ) {
			return (string) $this->values['attribute.type'];
		}
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param string $type Type of the attribute
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['attribute.type'] = $this->checkCode( $type );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode()
	{
		if( isset( $this->values['attribute.code'] ) ) {
			return (string) $this->values['attribute.code'];
		}

		return '';
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( strlen( $code ) > 255 ) {
			throw new \Aimeos\MShop\Attribute\Exception( sprintf( 'Code must not be longer than 255 characters' ) );
		}

		if( (string) $code !== $this->getCode() )
		{
			$this->values['attribute.code'] = (string) $code;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		if( isset( $this->values['attribute.label'] ) ) {
			return (string) $this->values['attribute.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values['attribute.label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['attribute.status'] ) ) {
			return (int) $this->values['attribute.status'];
		}

		return 1;
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['attribute.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition()
	{
		if( isset( $this->values['attribute.position'] ) ) {
			return (int) $this->values['attribute.position'];
		}

		return 0;
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param integer $pos Position of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		if( (int) $pos !== $this->getPosition() )
		{
			$this->values['attribute.position'] = (int) $pos;
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
		return 'attribute';
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
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'attribute.domain': $item = $item->setDomain( $value ); break;
				case 'attribute.code': $item = $item->setCode( $value ); break;
				case 'attribute.status': $item = $item->setStatus( $value ); break;
				case 'attribute.type': $item = $item->setType( $value ); break;
				case 'attribute.position': $item = $item->setPosition( $value ); break;
				case 'attribute.label': $item = $item->setLabel( $value ); break;
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

		$list['attribute.domain'] = $this->getDomain();
		$list['attribute.type'] = $this->getType();
		$list['attribute.code'] = $this->getCode();
		$list['attribute.label'] = $this->getLabel();
		$list['attribute.status'] = $this->getStatus();
		$list['attribute.position'] = $this->getPosition();

		if( $private === true ) {
			$list['attribute.key'] = $this->getKey();
		}

		return $list;
	}

}
