<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @return integer|null Parent ID of the common list item
	 */
	public function getParentId()
	{
		if( isset( $this->values[$this->prefix . 'parentid'] ) ) {
			return (int) $this->values[$this->prefix . 'parentid'];
		}

		return null;
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param integer $parentid New parent ID of the common list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setParentId( $parentid )
	{
		if( $parentid == $this->getParentId() ) { return $this; }

		$this->values[$this->prefix . 'parentid'] = (int) $parentid;
		$this->setModified();

		return $this;
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
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values[$this->prefix . 'domain'] = (string) $domain;
		$this->setModified();

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
		if( $refid == $this->getRefId() ) { return $this; }

		$this->values[$this->prefix . 'refid'] = (string) $refid;
		$this->setModified();

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
		if( $date === $this->getDateStart() ) { return $this; }

		$this->values[$this->prefix . 'datestart'] = $this->checkDateFormat( $date );
		$this->setModified();

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
		if( $date === $this->getDateEnd() ) { return $this; }

		$this->values[$this->prefix . 'dateend'] = $this->checkDateFormat( $date );
		$this->setModified();

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

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values[$this->prefix . 'typename'] ) ) {
			return (string) $this->values[$this->prefix . 'typename'];
		}

		return null;
	}


	/**
	 * Returns the type id of the list item.
	 *
	 * @return integer|null Type id of the list item
	 */
	public function getTypeId()
	{
		if( isset( $this->values[$this->prefix . 'typeid'] ) ) {
			return (int) $this->values[$this->prefix . 'typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type id of the list item.
	 *
	 * @param integer|null $typeid type id of the list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return $this; }

		$this->values[$this->prefix . 'typeid'] = (int) $typeid;
		$this->setModified();

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
		if( $pos == $this->getPosition() ) { return $this; }

		$this->values[$this->prefix . 'position'] = (int) $pos;
		$this->setModified();

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
		if( $status == $this->getStatus() ) { return $this; }

		$this->values[$this->prefix . 'status'] = (int) $status;
		$this->setModified();

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
	 * @param \Aimeos\MShop\Common\Item\Iface $refItem Item referenced by the list item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Lists item for chaining method calls
	 */
	public function setRefItem( \Aimeos\MShop\Common\Item\Iface $refItem )
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
				case $this->prefix . 'parentid': $this->setParentId( $value ); break;
				case $this->prefix . 'typeid': $this->setTypeId( $value ); break;
				case $this->prefix . 'domain': $this->setDomain( $value ); break;
				case $this->prefix . 'refid': $this->setRefId( $value ); break;
				case $this->prefix . 'datestart': $this->setDateStart( $value ); break;
				case $this->prefix . 'dateend': $this->setDateEnd( $value ); break;
				case $this->prefix . 'config': $this->setConfig( $value ); break;
				case $this->prefix . 'position': $this->setPosition( $value ); break;
				case $this->prefix . 'status': $this->setStatus( $value ); break;
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
		$list = parent::toArray( $private );

		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'refid'] = $this->getRefId();
		$list[$this->prefix . 'datestart'] = $this->getDateStart();
		$list[$this->prefix . 'dateend'] = $this->getDateEnd();
		$list[$this->prefix . 'config'] = $this->getConfig();
		$list[$this->prefix . 'position'] = $this->getPosition();
		$list[$this->prefix . 'status'] = $this->getStatus();
		$list[$this->prefix . 'typename'] = $this->getTypeName();
		$list[$this->prefix . 'type'] = $this->getType();

		if( $private === true )
		{
			$list[$this->prefix . 'parentid'] = $this->getParentId();
			$list[$this->prefix . 'typeid'] = $this->getTypeId();
		}

		return $list;
	}

}
