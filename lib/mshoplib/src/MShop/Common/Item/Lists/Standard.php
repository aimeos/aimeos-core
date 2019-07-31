<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Lists;


/**
 * Default implementation of the list item.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Lists\Iface
{
	use \Aimeos\MShop\Common\Item\Config\Traits;


	private $prefix;
	private $values;
	private $refItem;


	/**
	 * Initializes the list item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Associative list of key/value pairs of the list item
	 */
	public function __construct( $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = (string) $prefix;
		$this->values = $values;
	}




	/**
	 * Returns the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @return string|null Parent ID of the common list item
	 */
	public function getParentId()
	{
		if( isset( $this->values[$this->prefix . 'parentid'] ) ) {
			return (string) $this->values[$this->prefix . 'parentid'];
		}

		return null;
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param string $parentid New parent ID of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setParentId( $parentid )
	{
		if( (string) $parentid !== $this->getParentId() )
		{
			$this->values[$this->prefix . 'parentid'] = (int) $parentid;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the unique key of the list item
	 *
	 * @return string Unique key consisting of domain/type/refid
	 */
	public function getKey()
	{
		return $this->getDomain() . '|' . $this->getType() . '|' . $this->getRefId();
	}


	/**
	 * Returns the domain of the common list item, e.g. text or media.
	 *
	 * @return string Domain of the common list item
	 */
	public function getDomain()
	{
		if( isset( $this->values[$this->prefix . 'domain'] ) ) {
			return (string) $this->values[$this->prefix . 'domain'];
		}

		return '';
	}


	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
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
	 * Returns the reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @return string Reference id of the common list item
	 */
	public function getRefId()
	{
		if( isset( $this->values[$this->prefix . 'refid'] ) ) {
			return (string) $this->values[$this->prefix . 'refid'];
		}

		return '';
	}


	/**
	 * Sets the new reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setRefId( $refid )
	{
		if( (string) $refid !== $this->getRefId() )
		{
			$this->values[$this->prefix . 'refid'] = (string) $refid;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string|null Start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateStart()
	{
		if( isset( $this->values[$this->prefix . 'datestart'] ) ) {
			return (string) $this->values[$this->prefix . 'datestart'];
		}

		return null;
	}


	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		if( (string) $date !== $this->getDateStart() )
		{
			$this->values[$this->prefix . 'datestart'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string|null End date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateEnd()
	{
		if( isset( $this->values[$this->prefix . 'dateend'] ) ) {
			return (string) $this->values[$this->prefix . 'dateend'];
		}

		return null;
	}


	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( (string) $date !== $this->getDateEnd() )
		{
			$this->values[$this->prefix . 'dateend'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the type of the list item.
	 *
	 * @return string|null Type of the list item
	 */
	public function getType()
	{
		if( isset( $this->values[$this->prefix . 'type'] ) ) {
			return (string) $this->values[$this->prefix . 'type'];
		}
	}



	/**
	 * Sets the new type of the list item.
	 *
	 * @param string $type type of the list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type != $this->getType() )
		{
			$this->values[$this->prefix . 'type'] = $this->checkCode( $type );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the position of the list item.
	 *
	 * @return integer Position of the list item
	 */
	public function getPosition()
	{
		if( isset( $this->values[$this->prefix . 'position'] ) ) {
			return (int) $this->values[$this->prefix . 'position'];
		}

		return 0;
	}


	/**
	 * Sets the new position of the list item.
	 *
	 * @param integer $pos position of the list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
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
	 * Returns the status of the list item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values[$this->prefix . 'status'] ) ) {
			return (int) $this->values[$this->prefix . 'status'];
		}

		return 1;
	}


	/**
	 * Sets the new status of the list item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
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
	 * Returns the configuration of the list item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig()
	{
		if( isset( $this->values[$this->prefix . 'config'] ) ) {
			return (array) $this->values[$this->prefix . 'config'];
		}

		return [];
	}


	/**
	 * Sets the new configuration for the list item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		$this->values[$this->prefix . 'config'] = $config;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the referenced item if it's available.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface Referenced list item
	 */
	public function getRefItem()
	{
		return $this->refItem;
	}


	/**
	 * Stores the item referenced by the list item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Item referenced by the list item or null for no reference
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setRefItem( \Aimeos\MShop\Common\Item\Iface $refItem = null )
	{
		$this->refItem = $refItem;

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
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->values['date'] )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->values['date'] );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface List item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case $this->prefix . 'type': $item = $item->setType( $value ); break;
				case $this->prefix . 'domain': $item = $item->setDomain( $value ); break;
				case $this->prefix . 'refid': $item = $item->setRefId( $value ); break;
				case $this->prefix . 'datestart': $item = $item->setDateStart( $value ); break;
				case $this->prefix . 'dateend': $item = $item->setDateEnd( $value ); break;
				case $this->prefix . 'config': $item = $item->setConfig( $value ); break;
				case $this->prefix . 'position': $item = $item->setPosition( $value ); break;
				case $this->prefix . 'status': $item = $item->setStatus( $value ); break;
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

		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'refid'] = $this->getRefId();
		$list[$this->prefix . 'datestart'] = $this->getDateStart();
		$list[$this->prefix . 'dateend'] = $this->getDateEnd();
		$list[$this->prefix . 'config'] = $this->getConfig();
		$list[$this->prefix . 'position'] = $this->getPosition();
		$list[$this->prefix . 'status'] = $this->getStatus();
		$list[$this->prefix . 'type'] = $this->getType();

		if( $private === true )
		{
			$list[$this->prefix . 'key'] = $this->getKey();
			$list[$this->prefix . 'parentid'] = $this->getParentId();
		}

		return $list;
	}

}
