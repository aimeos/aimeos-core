<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	/**
	 * Initializes the order item base service attribute item.
	 *
	 * @param array $values Associative array of key/value pairs.
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.base.service.attribute.', $values );
	}


	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return string Site ID (or null if not available)
	 */
	public function getSiteId() : string
	{
		return $this->get( 'order.base.service.attribute.siteid', '' );
	}


	/**
	 * Sets the site ID of the item.
	 *
	 * @param string $value Unique site ID of the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.siteid', $value );
	}


	/**
	 * Returns the original attribute ID of the service attribute item.
	 *
	 * @return string Attribute ID of the service attribute item
	 */
	public function getAttributeId() : string
	{
		return $this->get( 'order.base.service.attribute.attributeid', '' );
	}


	/**
	 * Sets the original attribute ID of the service attribute item.
	 *
	 * @param string|null $id Attribute ID of the service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setAttributeId( ?string $id ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.attributeid', (string) $id );
	}


	/**
	 * Returns the ID of the ordered service item as parent
	 *
	 * @return string|null ID of the ordered service item
	 */
	public function getParentId() : ?string
	{
		return $this->get( 'order.base.service.attribute.parentid' );
	}


	/**
	 * Sets the ID of the ordered service item as parent
	 *
	 * @param string|null $id ID of the ordered service item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setParentId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.base.service.attribute.parentid', $id );
	}


	/**
	 * Returns the type of the service attribute item.
	 *
	 * @return string Type of the service attribute item
	 */
	public function getType() : string
	{
		return $this->get( 'order.base.service.attribute.type', '' );
	}


	/**
	 * Sets a new type for the service attribute item.
	 *
	 * @param string $type Type of the service attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.base.service.attribute.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string Code of the service attribute item
	 */
	public function getCode() : string
	{
		return $this->get( 'order.base.service.attribute.code', '' );
	}


	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.code', $this->checkCode( $code, 255 ) );
	}


	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName() : string
	{
		return $this->get( 'order.base.service.attribute.name', '' );
	}


	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string|null $name Name as defined by the service provider
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setName( ?string $name ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.name', (string) $name );
	}


	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return string|array Service attribute item value
	 */
	public function getValue()
	{
		return $this->get( 'order.base.service.attribute.value', '' );
	}


	/**
	 * Sets a new value for the service item.
	 *
	 * @param string|array $value service attribute item value
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setValue( $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.value', $value );
	}


	/**
	 * Returns the quantity of the service attribute.
	 *
	 * @return float Quantity of the service attribute
	 */
	public function getQuantity() : float
	{
		return $this->get( 'order.base.service.attribute.quantity', 1 );
	}


	/**
	 * Sets the quantity of the service attribute.
	 *
	 * @param float $value Quantity of the service attribute
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function setQuantity( float $value ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		return $this->set( 'order.base.service.attribute.quantity', $value );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/base/service/attribute';
	}


	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface $item Attribute item to copy from
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order base service attribute item for chaining method calls
	 */
	public function copyFrom( \Aimeos\MShop\Attribute\Item\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		$this->setSiteId( $item->getSiteId() );
		$this->setAttributeId( $item->getId() );
		$this->setName( $item->getName() );
		$this->setCode( $item->getType() );
		$this->setValue( $item->getCode() );

		$this->setModified();

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface Order service attribute item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.service.attribute.attributeid': !$private ?: $item = $item->setAttributeId( $value ); break;
				case 'order.base.service.attribute.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'order.base.service.attribute.siteid': !$private ?: $item = $item->setSiteId( $value ); break;
				case 'order.base.service.attribute.type': $item = $item->setType( $value ); break;
				case 'order.base.service.attribute.name': $item = $item->setName( $value ); break;
				case 'order.base.service.attribute.code': $item = $item->setCode( $value ); break;
				case 'order.base.service.attribute.value': $item = $item->setValue( $value ); break;
				case 'order.base.service.attribute.quantity': $item = $item->setQuantity( $value ); break;
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

		$list['order.base.service.attribute.type'] = $this->getType();
		$list['order.base.service.attribute.name'] = $this->getName();
		$list['order.base.service.attribute.code'] = $this->getCode();
		$list['order.base.service.attribute.value'] = $this->getValue();
		$list['order.base.service.attribute.quantity'] = $this->getQuantity();

		if( $private === true )
		{
			$list['order.base.service.attribute.parentid'] = $this->getParentId();
			$list['order.base.service.attribute.attributeid'] = $this->getAttributeId();
		}

		return $list;
	}
}
