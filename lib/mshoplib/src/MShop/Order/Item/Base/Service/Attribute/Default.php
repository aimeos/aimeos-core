<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Service_Attribute_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Order_Item_Base_Service_Attribute_Interface
{
	private $_values;


	/**
	 * Initializes the order item base service attribute item.
	 *
	 * @param array $values Associative array of key/value pairs.
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('order.base.service.attribute.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the order service id of the order service attribute if available.
	 *
	 * @return integer|null Returns the order service id of the order service attribute if available.
	 */
	public function getServiceId()
	{
		return ( isset( $this->_values['ordservid'] ) ? (int) $this->_values['ordservid'] : null );
	}


	/**
	 * Sets the order service id.
	 *
	 * @param integer Order service id for the order service attribute item.
	 */
	public function setServiceId( $id )
	{
		if ( $id == $this->getServiceId() ) { return; }

		$this->_values['ordservid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Type of the service attribute item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : '' );
	}


	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string $name Type as defined by the service provider
	 */
	public function setType($type)
	{
		if ( $type == $this->getType() ) { return; }

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName()
	{
		return ( isset( $this->_values['name'] ) ? (string) $this->_values['name'] : '' );
	}


	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string $name Name as defined by the service provider
	 */
	public function setName( $name )
	{
		$this->_values['name'] = (string) $name;
		$this->setModified();
	}


	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string Code of the service attribute item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return mixed Service attribute item value.
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? $this->_values['value'] : '' );
	}


	/**
	 * Sets a new value for the service item.
	 *
	 * @param mixed $value service attribute item value
	 */
	public function setValue( $value )
	{
		if ( $value == $this->getValue() ) { return; }

		$this->_values['value'] = $value;
		$this->setModified();
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param MShop_Attribute_Item_Interface $item Attribute item to copy from
	 */
	public function copyFrom( MShop_Attribute_Item_Interface $item )
	{
		$this->setName( $item->getName() );
		$this->setCode( $item->getType() );
		$this->setValue( $item->getCode() );

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

		$list['order.base.service.attribute.ordservid'] = $this->getServiceId();
		$list['order.base.service.attribute.type'] = $this->getType();
		$list['order.base.service.attribute.name'] = $this->getName();
		$list['order.base.service.attribute.code'] = $this->getCode();
		$list['order.base.service.attribute.value'] = $this->getValue();

		return $list;
	}
}