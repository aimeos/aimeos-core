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
		return md5( $this->getDomain() . '|' . $this->getType() . '|' . $this->getCode() );
	}


	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain()
	{
		return (string) $this->get( 'attribute.domain', '' );
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		return $this->set( 'attribute.domain', (string) $domain );
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType()
	{
		return $this->get( 'attribute.type' );
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param string $type Type of the attribute
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'attribute.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode()
	{
		return (string) $this->get( 'attribute.code', '' );
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setCode( $code )
	{
		return $this->set( 'attribute.code', $this->checkCode( $code, 255 ) );
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return (string) $this->get( 'attribute.label', '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setLabel( $label )
	{
		return $this->set( 'attribute.label', (string) $label );
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'attribute.status', 1 );
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'attribute.status', (int) $status );
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition()
	{
		return (int) $this->get( 'attribute.position', 0 );
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param integer $pos Position of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setPosition( $pos )
	{
		return $this->set( 'attribute.position', (int) $pos );
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
