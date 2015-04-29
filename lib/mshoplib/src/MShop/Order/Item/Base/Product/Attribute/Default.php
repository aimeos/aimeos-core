<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default product attribute item implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Product_Attribute_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Order_Item_Base_Product_Attribute_Interface
{
	private $_values;

	/**
	 * Initializes the order product attribute instance.
	 *
	 * @param array $values Associative array of order product attribute values. Possible
	 * keys: 'id', 'ordprodid', 'value', 'code', 'mtime'
	 */
	public function __construct(array $values = array())
	{
		parent::__construct('order.base.product.attribute.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the original attribute ID of the product attribute item.
	 *
	 * @return string Attribute ID of the product attribute item
	 */
	public function getAttributeId()
	{
		return ( isset( $this->_values['attrid'] ) ? (string) $this->_values['attrid'] : '' );
	}


	/**
	 * Sets the original attribute ID of the product attribute item.
	 *
	 * @param string $id Attribute ID of the product attribute item
	 */
	public function setAttributeId( $id )
	{
		if ( $id == $this->getAttributeId() ) { return; }

		$this->_values['attrid'] = (string) $id;
		$this->setModified();
	}


	/**
	 * Returns the product ID of the ordered product.
	 *
	 * @return string|null Product ID of the ordered product
	 */
	public function getProductId()
	{
		return ( isset( $this->_values['ordprodid'] ) ? (string) $this->_values['ordprodid'] : null );
	}


	/**
	 * Sets the product ID of the ordered product.
	 *
	 * @param string $id Product ID of the ordered product
	 */
	public function setProductId( $id )
	{
		if ( $id == $this->getProductId() ) { return; }

		$this->_values['ordprodid'] = (string) $id;
		$this->setModified();
	}


	/**
	 * Returns the value of the product attribute.
	 *
	 * @return string Value of the product attribute
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : '' );
	}


	/**
	 * Sets the value of the product attribute.
	 *
	 * @param string $type Type of the product attribute
	 */
	public function setType( $type )
	{
		if ( $type == $this->getType() ) { return; }

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Returns the code of the product attibute.
	 *
	 * @return string Code of the attribute
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the product attribute.
	 *
	 * @param string $code Code of the attribute
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the value of the product attribute.
	 *
	 * @return string Value of the product attribute
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? (string) $this->_values['value'] : '' );
	}


	/**
	 * Sets the value of the product attribute.
	 *
	 * @param string $value Value of the product attribute
	 */
	public function setValue($value)
	{
		if ( $value == $this->getValue() ) { return; }

		$this->_values['value'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the localized name of the product attribute.
	 *
	 * @return string Localized name of the product attribute
	 */
	public function getName()
	{
		return ( isset( $this->_values['name'] ) ? (string) $this->_values['name'] : '' );
	}


	/**
	 * Sets the localized name of the product attribute.
	 *
	 * @param string $name Localized name of the product attribute
	 */
	public function setName($name)
	{
		if ( $name == $this->getName() ) { return; }

		$this->_values['name'] = (string) $name;
		$this->setModified();
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param MShop_Attribute_Item_Interface $item Attribute item to copy from
	 */
	public function copyFrom( MShop_Attribute_Item_Interface $item )
	{
		$this->setAttributeId( $item->getId() );
		$this->setName( $item->getName() );
		$this->setCode( $item->getType() );
		$this->setValue( $item->getCode() );

		$this->setModified();
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
				case 'order.base.product.attribute.attrid': $this->setAttributeId( $value ); break;
				case 'order.base.product.attribute.productid': $this->setProductId( $value ); break;
				case 'order.base.product.attribute.type': $this->setType( $value ); break;
				case 'order.base.product.attribute.code': $this->setCode( $value ); break;
				case 'order.base.product.attribute.value': $this->setValue( $value ); break;
				case 'order.base.product.attribute.name': $this->setName( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['order.base.product.attribute.attrid'] = $this->getAttributeId();
		$list['order.base.product.attribute.productid'] = $this->getProductId();
		$list['order.base.product.attribute.type'] = $this->getType();
		$list['order.base.product.attribute.code'] = $this->getCode();
		$list['order.base.product.attribute.value'] = $this->getValue();
		$list['order.base.product.attribute.name'] = $this->getName();

		return $list;
	}

}
