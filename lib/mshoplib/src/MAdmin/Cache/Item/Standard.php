<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
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
		if( isset( $this->values['id'] ) ) {
			return (string) $this->values['id'];
		}
	}


	/**
	 * Sets the unique ID of the item.
	 *
	 * @param string $id Unique ID of the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setId( $id )
	{
		if( (string) $id !== $this->getId() )
		{
			$this->values['id'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		if( isset( $this->values['siteid'] ) ) {
			return (string) $this->values['siteid'];
		}
	}


	/**
	 * Returns the value associated to the key.
	 *
	 * @return string Returns the value of the item
	 */
	public function getValue()
	{
		if( isset( $this->values['value'] ) ) {
			return (string) $this->values['value'];
		}

		return '';
	}


	/**
	 * Sets the new value of the item.
	 *
	 * @param string $value Value of the item or null for no expiration
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setValue( $value )
	{
		if( (string) $value !== $this->getValue() )
		{
			$this->values['value'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the expiration time of the item.
	 *
	 * @return string|null Expiration time of the item or null for no expiration
	 */
	public function getTimeExpire()
	{
		if( isset( $this->values['expire'] ) ) {
			return (string) $this->values['expire'];
		}
	}


	/**
	 * Sets the new expiration time of the item.
	 *
	 * @param string|null $timestamp Expiration time of the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setTimeExpire( $timestamp )
	{
		if( $timestamp !== $this->getValue() )
		{
			$this->values['expire'] = $this->checkDateFormat( $timestamp );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the tags associated to the item.
	 *
	 * @return array Tags associated to the item
	 */
	public function getTags()
	{
		if( isset( $this->values['tags'] ) ) {
			return (array) $this->values['tags'];
		}

		return [];
	}


	/**
	 * Sets the new tags associated to the item.
	 *
	 * @param array Tags associated to the item
	 * @return \Aimeos\MAdmin\Cache\Item\Iface Cache item for chaining method calls
	 */
	public function setTags( array $tags )
	{
		$this->values['tags'] = $tags;
		$this->setModified();

		return $this;
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
