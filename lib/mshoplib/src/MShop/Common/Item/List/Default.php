<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 * Returns the status of the lsit item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
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
		$list[$this->_prefix . 'position'] = $this->getPosition();
		$list[$this->_prefix . 'status'] = $this->getStatus();

		return $list;
	}

}
