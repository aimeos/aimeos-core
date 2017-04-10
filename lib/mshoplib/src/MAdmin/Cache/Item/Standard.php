<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MAdmin
 * @subpackage Cache
 */


namespace Aimeos\MAdmin\Cache\Item;


/**
 * Default cache item implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MAdmin\Cache\Item\Iface
{
	private $values;


	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'cache.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		return ( isset( $this->values['id'] ) ? (string) $this->values['id'] : null );
	}


	/**
	 * Sets the unique ID of the item.
	 *
	 * @param integer $id Unique ID of the item
	 */
	public function setId( $id )
	{
		if( $id === $this->getId() ) { return; }

		$this->values['id'] = (string) $id;
		$this->setModified();
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return integer|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		return ( isset( $this->values['siteid'] ) ? (int) $this->values['siteid'] : null );
	}


	/**
	 * Returns the value associated to the key.
	 *
	 * @return string Returns the value of the item
	 */
	public function getValue()
	{
		return ( isset( $this->values['value'] ) ? (string) $this->values['value'] : '' );
	}


	/**
	 * Sets the new value of the item.
	 *
	 * @param string $value Value of the item or null for no expiration
	 */
	public function setValue( $value )
	{
		$this->values['value'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the expiration time of the item.
	 *
	 * @return string|null Expiration time of the item or null for no expiration
	 */
	public function getTimeExpire()
	{
		return ( isset( $this->values['expire'] ) ? (string) $this->values['expire'] : null );
	}


	/**
	 * Sets the new expiration time of the item.
	 *
	 * @param string|null $timestamp Expiration time of the item
	 */
	public function setTimeExpire( $timestamp )
	{
		if( $timestamp !== null )
		{
			$timestamp = (string) $timestamp;
			$this->checkDateFormat( $timestamp );
		}

		$this->values['expire'] = $timestamp;
		$this->setModified();
	}


	/**
	 * Returns the tags associated to the item.
	 *
	 * @return array Tags associated to the item
	 */
	public function getTags()
	{
		return ( isset( $this->values['tags'] ) ? (array) $this->values['tags'] : [] );
	}


	/**
	 * Sets the new tags associated to the item.
	 *
	 * @param array Tags associated to the item
	 */
	public function setTags( array $tags )
	{
		$this->values['tags'] = $tags;
		$this->setModified();
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'cache';
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

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'cache.id': $this->setId( $value ); break;
				case 'cache.value': $this->setValue( $value ); break;
				case 'cache.expire': $this->setTimeExpire( $value ); break;
				case 'cache.tags': $this->setTags( $value ); break;
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
		$list = [];

		$list['cache.id'] = $this->getId();
		$list['cache.value'] = $this->getValue();
		$list['cache.expire'] = $this->getTimeExpire();
		$list['cache.tags'] = $this->getTags();

		if( $private === true ) {
			$list['cache.siteid'] = $this->getSiteId();
		}

		return $list;
	}
}
