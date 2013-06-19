<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Service item with common methods.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Service_Item_Interface
{
	private $_values;

	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 * @param array List of items implementing MShop_Common_List_Item_Interface
	 * @param array List of items implementing MShop_Text_Item_Interface
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct('service.', $values, $listItems, $refItems);

		$this->_values = $values;
	}


	/**
	 * Returns the position of the service item in the list of deliveries.
	 *
	 * @return integer Position in item list
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Sets the new position of the service item in the list of deliveries.
	 *
	 * @param integer $position Position in item list
	 */
	public function setPosition( $pos )
	{
		if ( $pos == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the code of the service item payment if available.
	 *
	 * @return string
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the service item payment.
	 *
	 * @param string code of the service item payment
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the type ID of the service item if available.
	 *
	 * @return integer Service item type ID
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the type ID of the service item.
	 *
	 * @param integer Type ID of the service item
	 */
	public function setTypeId( $typeId )
	{
		if ( $typeId == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeId;
		$this->setModified();
	}


	/**
	 * Returns the type of the service item if available.
	 *
	 * @return string Service item type
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider()
	{
		return ( isset( $this->_values['provider'] ) ? (string) $this->_values['provider'] : '' );
	}


	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 */
	public function setProvider( $provider )
	{
		if ( $provider == $this->getProvider() ) { return; }

		$this->_values['provider'] = (string) $provider;
		$this->setModified();
	}


	/**
	 * Returns the label of the service item payment if available.
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label of the service item payment.
	 *
	 * @param string label of the service item payment
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig()
	{
		return ( isset( $this->_values['config'] ) ? $this->_values['config'] : array() );
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 */
	public function setConfig( array $config )
	{
		$this->_values['config'] = $config;
		$this->setModified();
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

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

		$list['service.typeid'] = $this->getTypeId();
		$list['service.type'] = $this->getType();
		$list['service.code'] = $this->getCode();
		$list['service.label'] = $this->getLabel();
		$list['service.provider'] = $this->getProvider();
		$list['service.position'] = $this->getPosition();
		$list['service.config'] = $this->getConfig();
		$list['service.status'] = $this->getStatus();

		return $list;
	}

}
