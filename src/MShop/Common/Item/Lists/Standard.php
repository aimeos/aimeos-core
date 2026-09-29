<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
		// @phpstan-ignore return.type
		return $this->get( $this->prefix . 'parentid' );
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param string|null $parentid New parent ID of the common list item
	 * @return static Lists item for chaining method calls
	 */
	public function setParentId( ?string $parentid ) : static
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
		return (string) $this->get( $this->prefix . 'domain', '' );
	}


	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 * @return static Lists item for chaining method calls
	 */
	public function setDomain( string $domain ) : static
	{
		return $this->set( $this->prefix . 'domain', $this->checkDomain( $domain ) );
	}


	/**
	 * Returns the reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @return string Reference id of the common list item
	 */
	public function getRefId() : string
	{
		return (string) $this->get( $this->prefix . 'refid', '' );
	}


	/**
	 * Sets the new reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 * @return static Lists item for chaining method calls
	 */
	public function setRefId( string $refid ) : static
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
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string|null $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return static Lists item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : static
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
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string|null $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 * @return static Lists item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : static
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
		return (int) $this->get( $this->prefix . 'position', 0 );
	}


	/**
	 * Sets the new position of the list item.
	 *
	 * @param int $pos position of the list item
	 * @return static Lists item for chaining method calls
	 */
	public function setPosition( int $pos ) : static
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
		return (int) $this->get( $this->prefix . 'status', 1 );
	}


	/**
	 * Sets the new status of the list item.
	 *
	 * @param int $status Status of the item
	 * @return static Lists item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( $this->prefix . 'status', $status );
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
	 * @return static Lists item for chaining method calls
	 */
	public function setRefItem( ?\Aimeos\MShop\Common\Item\Iface $refItem ) : static
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
		return (string) $this->get( $this->prefix . 'type', 'default' );
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
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static List item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case $this->prefix . 'parentid': !$private ?: $item->setParentId( (string) $value ); break;
				case $this->prefix . 'domain': $item->setDomain( (string) $value ); break;
				case $this->prefix . 'type': $item->setType( (string) $value ); break;
				case $this->prefix . 'refid': $item->setRefId( (string) $value ); break;
				case $this->prefix . 'datestart': $item->setDateStart( $value !== null ? (string) $value : null ); break;
				case $this->prefix . 'dateend': $item->setDateEnd( $value !== null ? (string) $value : null ); break;
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
	 * @param bool $private True to return private properties, false for public only
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
