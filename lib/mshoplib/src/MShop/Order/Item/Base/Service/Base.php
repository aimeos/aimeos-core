<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Service;


/**
 * Abstract class \Aimeos\MShop\Order\Item\Base\Service\Base.
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Base implements Iface
{
	/**
	 * Delivery service.
	 */
	const TYPE_DELIVERY = 'delivery';

	/**
	 * Payment service.
	 */
	const TYPE_PAYMENT = 'payment';


	private $attributes;
	private $attributesMap;
	private $price;


	/**
	 * Initializes the order base service item
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price
	 * @param array $values Values to be set on initialisation
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface[] $attributes List of order service attribute items
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [] )
	{
		parent::__construct( 'order.base.service.', $values );

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface::class, $attributes );
		$this->attributes = $attributes;

		$this->price = $price;
	}


	/**
	 * Clones internal objects of the order base product item.
	 */
	public function __clone()
	{
		foreach( $this->attributes as $key => $item ) {
			$this->attributes[$key] = clone $item;
		}

		$this->price = clone $this->price;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'order/base/service';
	}


	/**
	 * Returns the value or list of values of the attribute item for the ordered service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param array|string $type Type or list of types of the service attribute items
	 * @return array|string|null Value or list of values of the attribute item for the ordered service and the given code
	 */
	public function getAttribute( string $code, $type = '' )
	{
		$list = [];
		$map = $this->getAttributeMap();

		foreach( (array) $type as $key )
		{
			if( isset( $map[$key][$code] ) )
			{
				foreach( $map[$key][$code] as $item ) {
					$list[] = $item->getValue();
				}
			}
		}

		return count( $list ) > 1 ? $list : ( reset( $list ) ?: null );
	}


	/**
	 * Returns the attribute item or list of attribute items for the ordered service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param array|string $type Type of the service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface|array|null
	 * 	Attribute item or list of items for the ordered service and the given code
	 */
	public function getAttributeItem( string $code, $type = '' )
	{
		$list = [];
		$map = $this->getAttributeMap();

		foreach( (array) $type as $key )
		{
			if( isset( $map[$key][$code] ) )
			{
				foreach( $map[$key][$code] as $item ) {
					$list[] = $item;
				}
			}
		}

		return count( $list ) > 1 ? $list : ( reset( $list ) ?: null );
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		$this->getAttributeMap();

		$type = $item->getType();
		$code = $item->getCode();
		$attrId = $item->getAttributeId();

		if( !isset( $this->attributesMap[$type][$code][$attrId] ) )
		{
			$this->attributesMap[$type][$code][$attrId] = $item;
			$this->attributes[] = $item;
		}

		$this->attributesMap[$type][$code][$attrId]->setValue( $item->getValue() );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the list of attribute items for the service.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	 */
	public function getAttributeItems( string $type = null ) : \Aimeos\Map
	{
		if( $type === null ) {
			return map( $this->attributes );
		}

		$list = [];

		foreach( $this->attributes as $attrItem )
		{
			if( $attrItem->getType() === $type ) {
				$list[] = $attrItem;
			}
		}

		return map( $list );
	}


	/**
	 * Sets the new list of attribute items for the service.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface[] $attributes List of order service attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface::class, $attributes );

		$this->attributes = is_map( $attributes ) ? $attributes->toArray() : $attributes;
		$this->attributesMap = null;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the price item for the product.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface
	{
		return $this->price;
	}


	/**
	 * Sets the price item for the product.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base product item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		if( $price !== $this->price )
		{
			$this->price = $price;
			$this->setModified();
		}

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order service item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );
		$price = $item->getPrice();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.service.price': $price = $price->setValue( $value ); break;
				case 'order.base.service.costs': $price = $price->setCosts( $value ); break;
				case 'order.base.service.rebate': $price = $price->setRebate( $value ); break;
				case 'order.base.service.taxrate': $price = $price->setTaxRate( $value ); break;
				case 'order.base.service.taxrates': $price = $price->setTaxRates( (array) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item->setPrice( $price );
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['order.base.service.price'] = $this->price->getValue();
		$list['order.base.service.costs'] = $this->price->getCosts();
		$list['order.base.service.rebate'] = $this->price->getRebate();
		$list['order.base.service.taxrate'] = $this->price->getTaxRate();
		$list['order.base.service.taxrates'] = $this->price->getTaxRates();

		return $list;
	}


	/**
	 * Checks if the given address type is valid
	 *
	 * @param string $value Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @throws \Aimeos\MShop\Order\Exception If type is invalid
	 */
	protected function checkType( string $value )
	{
		switch( $value )
		{
			case \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY:
			case \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT:
				return;
			default:
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Service of type "%1$s" not available', $value ) );
		}
	}


	/**
	 * Returns the attribute map for the service.
	 *
	 * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface as value
	 */
	protected function getAttributeMap() : array
	{
		if( !isset( $this->attributesMap ) )
		{
			$this->attributesMap = [];

			foreach( $this->attributes as $item ) {
				$this->attributesMap[$item->getType()][$item->getCode()][$item->getAttributeId()] = $item;
			}
		}

		return $this->attributesMap;
	}
}
