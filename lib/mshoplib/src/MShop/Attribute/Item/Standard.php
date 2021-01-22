<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;


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
	use ListsRef\Traits, PropertyRef\Traits  {
		ListsRef\Traits::__clone insteadof PropertyRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		PropertyRef\Traits::__clone as __cloneProperty;
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
	public function getKey() : string
	{
		return md5( $this->getDomain() . '|' . $this->getType() . '|' . $this->getCode() );
	}


	/**
	 * Returns the domain of the attribute item.
	 *
	 * @return string Returns the domain for this item e.g. text, media, price...
	 */
	public function getDomain() : string
	{
		return (string) $this->get( 'attribute.domain', '' );
	}


	/**
	 * Set the name of the domain for this attribute item.
	 *
	 * @param string $domain Name of the domain e.g. text, media, price...
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'attribute.domain', $domain );
	}


	/**
	 * Returns the type code of the attribute item.
	 *
	 * @return string|null Type code of the attribute item
	 */
	public function getType() : ?string
	{
		return $this->get( 'attribute.type' );
	}


	/**
	 * Sets the new type of the attribute.
	 *
	 * @param string $type Type of the attribute
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'attribute.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns a unique code of the attribute item.
	 *
	 * @return string Returns the code of the attribute item
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'attribute.code', '' );
	}


	/**
	 * Sets a unique code for the attribute item.
	 *
	 * @param string $code Code of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Attribute\Item\Iface
	{
		return $this->set( 'attribute.code', $this->checkCode( $code, 255 ) );
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string
	{
		return $this->get( 'attribute.label', '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Attribute\Item\Iface
	{
		return $this->set( 'attribute.label', $label );
	}


	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return int Returns the status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'attribute.status', 1 );
	}


	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'attribute.status', $status );
	}


	/**
	 * Gets the position of the attribute item.
	 *
	 * @return integer Position of the attribute item
	 */
	public function getPosition() : int
	{
		return $this->get( 'attribute.position', 0 );
	}


	/**
	 * Sets the position of the attribute item
	 *
	 * @param int $pos Position of the attribute item
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'attribute.position', $pos );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'attribute';
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
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'attribute.domain': $item = $item->setDomain( $value ); break;
				case 'attribute.code': $item = $item->setCode( $value ); break;
				case 'attribute.type': $item = $item->setType( $value ); break;
				case 'attribute.status': $item = $item->setStatus( (int) $value ); break;
				case 'attribute.position': $item = $item->setPosition( (int) $value ); break;
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
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
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
