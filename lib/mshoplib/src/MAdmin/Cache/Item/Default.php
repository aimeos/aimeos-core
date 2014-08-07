<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * Default cache item implementation.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class MAdmin_Cache_Item_Default
	extends MShop_Common_Item_Abstract
	implements MAdmin_Cache_Item_Interface
{
	private $_values;


	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array() )
	{
		$this->_values = $values;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		return ( isset( $this->_values['id'] ) ? (string) $this->_values['id'] : null );
	}


	/**
	 * Sets the unique ID of the item.
	 *
	 * @param integer $id Unique ID of the item
	 */
	public function setId( $id )
	{
		if( $id === $this->getId() ) { return; }

		$this->_values['id'] = (string) $id ;
		$this->setModified();
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return integer|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		return ( isset( $this->_values['siteid'] ) ? (int) $this->_values['siteid'] : null );
	}


	/**
	 * Returns the value associated to the key.
	 *
	 * @return string Returns the value of the item
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? (string) $this->_values['value'] : '' );
	}


	/**
	 * Sets the new value of the item.
	 *
	 * @param string $value Value of the item or null for no expiration
	 */
	public function setValue( $value )
	{
		$this->_values['value'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the expiration time of the item.
	 *
	 * @return string|null Expiration time of the item or null for no expiration
	 */
	public function getTimeExpire()
	{
		return ( isset( $this->_values['expire'] ) ? (string) $this->_values['expire'] : null );
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
			$this->_checkDateFormat( $timestamp );
		}

		$this->_values['expire'] = $timestamp;
		$this->setModified();
	}


	/**
	 * Returns the tags associated to the item.
	 *
	 * @return array Tags associated to the item
	 */
	public function getTags()
	{
		return ( isset( $this->_values['tags'] ) ? (array) $this->_values['tags'] : array() );
	}


	/**
	 * Sets the new tags associated to the item.
	 *
	 * @param array Tags associated to the item
	 */
	public function setTags( array $tags )
	{
		$this->_values['tags'] = $tags;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		return array(
			'cache.id' => $this->getId(),
			'cache.siteid' => $this->getSiteId(),
			'cache.value' => $this->getValue(),
			'cache.expire' => $this->getTimeExpire(),
		);
	}

}
