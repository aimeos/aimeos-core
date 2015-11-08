<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	public function __construct( $prefix, array $values = array() )
	{
		parent::__construct( $prefix, $values );

		$this->prefix = (string) $prefix;
		$this->values = $values;
	}




	/**
	 * Returns the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @return integer Parent ID of the common list item
	 */
	public function getParentId()
	{
		return ( isset( $this->values['parentid'] ) ? (int) $this->values['parentid'] : null );
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param integer $parentid New parent ID of the common list item
	 */
	public function setParentId( $parentid )
	{
		if( $parentid == $this->getParentId() ) { return; }

		$this->values['parentid'] = (int) $parentid;
		$this->setModified();
	}


	/**
	 * Returns the domain of the common list item, e.g. text or media.
	 *
	 * @return string Domain of the common list item
	 */
	public function getDomain()
	{
		return ( isset( $this->values['domain'] ) ? (string) $this->values['domain'] : '' );
	}


	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return; }

		$this->values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @return string Reference id of the common list item
	 */
	public function getRefId()
	{
		return ( isset( $this->values['refid'] ) ? (string) $this->values['refid'] : '' );
	}


	/**
	 * Sets the new reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 */
	public function setRefId( $refid )
	{
		if( $refid == $this->getRefId() ) { return; }

		$this->values['refid'] = (string) $refid;
		$this->setModified();
	}


	/**
	 * Returns the start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string Start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateStart()
	{
		return ( isset( $this->values['start'] ) ? (string) $this->values['start'] : null );
	}


	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateStart( $date )
	{
		if( $date === $this->getDateStart() ) { return; }

		$this->values['start'] = $this->checkDateFormat( $date );
		$this->setModified();
	}


	/**
	 * Returns the end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string End date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateEnd()
	{
		return ( isset( $this->values['end'] ) ? (string) $this->values['end'] : null );
	}


	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateEnd( $date )
	{
		if( $date === $this->getDateEnd() ) { return; }

		$this->values['end'] = $this->checkDateFormat( $date );
		$this->setModified();
	}


	/**
	 * Returns the type of the list item.
	 *
	 * @return string Type of the list item
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : null );
	}


	/**
	 * Returns the type id of the list item.
	 *
	 * @return integer Type id of the list item
	 */
	public function getTypeId()
	{
		return ( isset( $this->values['typeid'] ) ? (int) $this->values['typeid'] : null );
	}


	/**
	 * Sets the new type id of the list item.
	 *
	 * @param integer|null $typeid type id of the list item
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return; }

		$this->values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the position of the list item.
	 *
	 * @return integer Position of the list item
	 */
	public function getPosition()
	{
		return ( isset( $this->values['pos'] ) ? (int) $this->values['pos'] : 0 );
	}


	/**
	 * Sets the new position of the list item.
	 *
	 * @param integer $pos position of the list item
	 */
	public function setPosition( $pos )
	{
		if( $pos == $this->getPosition() ) { return; }

		$this->values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the status of the list item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 1 );
	}


	/**
	 * Sets the new status of the list item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) {
			return;
		}

		$this->values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the configuration of the list item.
	 *
	 * @return string Custom configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->values['config'] ) ? $this->values['config'] : array() );
	}


	/**
	 * Sets the new configuration for the list item.
	 *
	 * @param array $config Custom configuration values
	 */
	public function setConfig( array $config )
	{
		$this->values['config'] = $config;
		$this->setModified();
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
	 */
	public function setRefItem( \Aimeos\MShop\Common\Item\Iface $refItem )
	{
		$this->refItem = $refItem;
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
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
		$unknown = array();
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list[$this->prefix . 'parentid'] = $this->getParentId();
		$list[$this->prefix . 'typeid'] = $this->getTypeId();
		$list[$this->prefix . 'type'] = $this->getType();
		$list[$this->prefix . 'domain'] = $this->getDomain();
		$list[$this->prefix . 'refid'] = $this->getRefId();
		$list[$this->prefix . 'datestart'] = $this->getDateStart();
		$list[$this->prefix . 'dateend'] = $this->getDateEnd();
		$list[$this->prefix . 'config'] = $this->getConfig();
		$list[$this->prefix . 'position'] = $this->getPosition();
		$list[$this->prefix . 'status'] = $this->getStatus();

		return $list;
	}

}
