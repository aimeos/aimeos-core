<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Default implementation of the list item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_List_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Common_Item_List_Interface
{
	private $_prefix;
	private $_values;
	private $_refItem;


	/**
	 * Initializes the list item object.
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values Associative list of key/value pairs of the list item
	 */
	public function __construct( $prefix, array $values = array() )
	{
		parent::__construct( $prefix, $values );

		$this->_prefix = (string) $prefix;
		$this->_values = $values;
	}




	/**
	 * Returns the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @return integer Parent ID of the common list item
	 */
	public function getParentId()
	{
		return ( isset( $this->_values['parentid'] ) ? (int) $this->_values['parentid'] : null );
	}


	/**
	 * Sets the parent ID of the common list item,
	 * like the unique ID of a product or a tree node.
	 *
	 * @param integer $parentid New parent ID of the common list item
	 */
	public function setParentId( $parentid )
	{
		if ( $parentid == $this->getParentId() ) { return; }

		$this->_values['parentid'] = (int) $parentid;
		$this->setModified();
	}


	/**
	 * Returns the domain of the common list item, e.g. text or media.
	 *
	 * @return string Domain of the common list item
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
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
		return ( isset( $this->_values['refid'] ) ? (string) $this->_values['refid'] : '' );
	}


	/**
	 * Sets the new reference id of the common list item, like the unique id
	 * of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 */
	public function setRefId( $refid )
	{
		if ( $refid == $this->getRefId() ) { return; }

		$this->_values['refid'] = (string) $refid;
		$this->setModified();
	}


	/**
	 * Returns the start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string Start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateStart()
	{
		return ( isset( $this->_values['start'] ) ? (string) $this->_values['start'] : null );
	}


	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateStart( $date )
	{
		if ( $date === $this->getDateStart() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['start'] = ( $date !== null ? (string) $date : null );
		$this->setModified();
	}


	/**
	 * Returns the end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string End date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateEnd()
	{
		return ( isset( $this->_values['end'] ) ? (string) $this->_values['end'] : null );
	}


	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateEnd( $date )
	{
		if ( $date === $this->getDateEnd() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['end'] = ( $date !== null ? (string) $date : null );
		$this->setModified();
	}


	/**
	 * Returns the type of the list item.
	 *
	 * @return string Type of the list item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the type id of the list item.
	 *
	 * @return integer Type id of the list item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type id of the list item.
	 *
	 * @param integer|null $typeid type id of the list item
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the position of the list item.
	 *
	 * @return integer Position of the list item
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Sets the new position of the list item.
	 *
	 * @param integer $pos position of the list item
	 */
	public function setPosition( $pos )
	{
		if ( $pos == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the status of the list item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 1 );
	}


	/**
	 * Sets the new status of the list item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) {
			return;
		}

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the configuration of the list item.
	 *
	 * @return string Custom configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->_values['config'] ) ? $this->_values['config'] : array() );
	}


	/**
	 * Sets the new configuration for the list item.
	 *
	 * @param array $config Custom configuration values
	 */
	public function setConfig( array $config )
	{
		$this->_values['config'] = $config;
		$this->setModified();
	}


	/**
	 * Returns the referenced item if it's available.
	 *
	 * @return MShop_Common_Item_Interface Referenced list item
	 */
	public function getRefItem()
	{
		return $this->_refItem;
	}


	/**
	 * Stores the item referenced by the list item.
	 *
	 * @param MShop_Common_Item_Interface $refItem Item referenced by the list item
	 */
	public function setRefItem( MShop_Common_Item_Interface $refItem )
	{
		$this->_refItem = $refItem;
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
				case $this->_prefix . 'parentid': $this->setParentId( $value ); break;
				case $this->_prefix . 'typeid': $this->setTypeId( $value ); break;
				case $this->_prefix . 'domain': $this->setDomain( $value ); break;
				case $this->_prefix . 'refid': $this->setRefId( $value ); break;
				case $this->_prefix . 'datestart': $this->setDateStart( $value ); break;
				case $this->_prefix . 'dateend': $this->setDateEnd( $value ); break;
				case $this->_prefix . 'config': $this->setConfig( $value ); break;
				case $this->_prefix . 'position': $this->setPosition( $value ); break;
				case $this->_prefix . 'status': $this->setStatus( $value ); break;
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

		$list[$this->_prefix . 'parentid'] = $this->getParentId();
		$list[$this->_prefix . 'typeid'] = $this->getTypeId();
		$list[$this->_prefix . 'type'] = $this->getType();
		$list[$this->_prefix . 'domain'] = $this->getDomain();
		$list[$this->_prefix . 'refid'] = $this->getRefId();
		$list[$this->_prefix . 'datestart'] = $this->getDateStart();
		$list[$this->_prefix . 'dateend'] = $this->getDateEnd();
		$list[$this->_prefix . 'config'] = $this->getConfig();
		$list[$this->_prefix . 'position'] = $this->getPosition();
		$list[$this->_prefix . 'status'] = $this->getStatus();

		return $list;
	}

}
