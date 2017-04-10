<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Service\Attribute;


/**
 * Default order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
{
	private $values;


	/**
	 * Initializes the order item base service attribute item.
	 *
	 * @param array $values Associative array of key/value pairs.
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.base.service.attribute.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the original attribute ID of the service attribute item.
	 *
	 * @return string Attribute ID of the service attribute item
	 */
	public function getAttributeId()
	{
		if( isset( $this->values['order.base.service.attribute.attributeid'] ) ) {
			return (string) $this->values['order.base.service.attribute.attributeid'];
		}

		return '';
	}


	/**
	 * Sets the original attribute ID of the service attribute item.
	 *
	 * @param string $id Attribute ID of the service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setAttributeId( $id )
	{
		if( $id == $this->getAttributeId() ) { return $this; }

		$this->values['order.base.service.attribute.attributeid'] = (string) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the ID of the ordered service item as parent
	 *
	 * @return string|null ID of the ordered service item
	 */
	public function getParentId()
	{
		if( isset( $this->values['order.base.service.attribute.parentid'] ) ) {
			return (string) $this->values['order.base.service.attribute.parentid'];
		}

		return null;
	}


	/**
	 * Sets the ID of the ordered service item as parent
	 *
	 * @param integer $id ID of the ordered service item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setParentId( $id )
	{
		if( $id == $this->getParentId() ) { return $this; }

		$this->values['order.base.service.attribute.parentid'] = (int) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the type of the service attribute item.
	 *
	 * @return string Type of the service attribute item
	 */
	public function getType()
	{
		if( isset( $this->values['order.base.service.attribute.type'] ) ) {
			return (string) $this->values['order.base.service.attribute.type'];
		}

		return '';
	}


	/**
	 * Sets a new type for the service attribute item.
	 *
	 * @param string $type Type of the service attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return $this; }

		$this->values['order.base.service.attribute.type'] = (string) $type;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName()
	{
		if( isset( $this->values['order.base.service.attribute.name'] ) ) {
			return (string) $this->values['order.base.service.attribute.name'];
		}

		return '';
	}


	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string $name Name as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setName( $name )
	{
		if( $name == $this->getName() ) { return $this; }

		$this->values['order.base.service.attribute.name'] = (string) $name;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string Code of the service attribute item
	 */
	public function getCode()
	{
		if( isset( $this->values['order.base.service.attribute.code'] ) ) {
			return (string) $this->values['order.base.service.attribute.code'];
		}

		return '';
	}


	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code == $this->getCode() ) { return $this; }

		$this->values['order.base.service.attribute.code'] = (string) $this->checkCode( $code );;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return string|array Service attribute item value
	 */
	public function getValue()
	{
		if( isset( $this->values['order.base.service.attribute.value'] ) ) {
			return $this->values['order.base.service.attribute.value'];
		}

		return '';
	}


	/**
	 * Sets a new value for the service item.
	 *
	 * @param string|array $value service attribute item value
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setValue( $value )
	{
		if( $value == $this->getValue() ) { return $this; }

		$this->values['order.base.service.attribute.value'] = $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order/base/service/attribute';
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item )
	{
		$this->setAttributeId( $item->getId() );
		$this->setName( $item->getName() );
		$this->setCode( $item->getType() );
		$this->setValue( $item->getCode() );

		$this->setModified();

		return $this;
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
				case 'order.base.service.attribute.attrid': $this->setAttributeId( $value ); break;
				case 'order.base.service.attribute.parentid': $this->setParentId( $value ); break;
				case 'order.base.service.attribute.type': $this->setType( $value ); break;
				case 'order.base.service.attribute.name': $this->setName( $value ); break;
				case 'order.base.service.attribute.code': $this->setCode( $value ); break;
				case 'order.base.service.attribute.value': $this->setValue( $value ); break;
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

		$list['order.base.service.attribute.attrid'] = $this->getAttributeId();
		$list['order.base.service.attribute.type'] = $this->getType();
		$list['order.base.service.attribute.name'] = $this->getName();
		$list['order.base.service.attribute.code'] = $this->getCode();
		$list['order.base.service.attribute.value'] = $this->getValue();

		if( $private === true ) {
			$list['order.base.service.attribute.parentid'] = $this->getParentId();
		}

		return $list;
	}
}