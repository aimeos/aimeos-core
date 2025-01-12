<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;




	private string $date;
	private string $prefix;
	private ?\Aimeos\MShop\Common\Item\Iface $refItem = null;


	/**
	 * Initializes the list item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Associative list of key/value pairs of the list item
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values, str_replace( '.', '/', rtrim( $prefix, '.' ) ) );

		$this->date = isset( $values['.date'] ) ? $values['.date'] : date( 'Y-m-d H:i:s' );
		$this->prefix = (string) $prefix;
	}


	/**
	 * Returns the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @return string|null Parent ID of the common list item
	 */
	public function getParentId() : ?string
	{
		return $this->get( $this->prefix . 'parentid' );
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param string|null $parentid New parent ID of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'parentid', $parentid );
	}


	/**
	 * Returns the unique key of the list item
	 *
	 * @return string Unique key consisting of domain/type/refid
	 */
	public function getKey() : string
	{
		return $this->getDomain() . '|' . $this->getType() . '|' . $this->getRefId();
	}


	/**
	 * Returns the domain of the common list item, e.g. text or media.
	 *
	 * @return string Domain of the common list item
	 */
	public function getDomain() : string
	{
		return $this->get( $this->prefix . 'domain', '' );
	}


	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setDomain( string $domain ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'domain', $domain );
	}


	/**
	 * Returns the reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @return string Reference id of the common list item
	 */
	public function getRefId() : string
	{
		return $this->get( $this->prefix . 'refid', '' );
	}


	/**
	 * Sets the new reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setRefId( string $refid ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		return $this->set( $this->prefix . 'refid', $refid );
	}


	/**
	 * Returns the start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string|null Start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( $this->prefix . 'datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string|null $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'datestart', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string|null End date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( $this->prefix . 'dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string|null $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'dateend', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the position of the list item.
	 *
	 * @return int Position of the list item
	 */
	public function getPosition() : int
	{
		return $this->get( $this->prefix . 'position', 0 );
	}


	/**
	 * Sets the new position of the list item.
	 *
	 * @param int $pos position of the list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setPosition( int $pos ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'position', $pos );
	}


	/**
	 * Returns the status of the list item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( $this->prefix . 'status', 1 );
	}


	/**
	 * Sets the new status of the list item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'status', $status );
	}


	/**
	 * Returns the configuration of the list item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( $this->prefix . 'config', [] );
	}


	/**
	 * Sets the new configuration for the list item.
	 *
	 * @param array $config Custom configuration values
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( $this->prefix . 'config', $config );
	}


	/**
	 * Returns the referenced item if it's available.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface|null Referenced list item
	 */
	public function getRefItem() : ?\Aimeos\MShop\Common\Item\Iface
	{
		return $this->refItem;
	}


	/**
	 * Stores the item referenced by the list item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Item referenced by the list item or null for no reference
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setRefItem( ?\Aimeos\MShop\Common\Item\Iface $refItem ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		$this->refItem = $refItem;

		return $this;
	}


	/**
	 * Returns the type of the text item.
	 * Overwritten for different default value.
	 *
	 * @return string Type of the text item
	 */
	public function getType() : string
	{
		return $this->get( $this->prefix . 'type', 'default' );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->date )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->date );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface List item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item->setParentId( $value ); break;
				case $this->prefix . 'domain': $item->setDomain( $value ); break;
				case $this->prefix . 'type': $item->setType( $value ); break;
				case $this->prefix . 'refid': $item->setRefId( $value ); break;
				case $this->prefix . 'datestart': $item->setDateStart( $value ); break;
				case $this->prefix . 'dateend': $item->setDateEnd( $value ); break;
				case $this->prefix . 'status': $item->setStatus( (int) $value ); break;
				case $this->prefix . 'config': $item->setConfig( (array) $value ); break;
				case $this->prefix . 'position': $item->setPosition( (int) $value ); break;
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
