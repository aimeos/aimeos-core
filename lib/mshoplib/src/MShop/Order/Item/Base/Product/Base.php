<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Product;


/**
 * Basket item abstract class defining available flags.
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Base
{
	/**
	 * No flag used.
	 * No order product flag set.
	 */
	const FLAG_NONE = 0;

	/**
	 * Product is immutable.
	 * Ordered product can't be modifed or deleted by the customer because it
	 * was e.g. added by a coupon provider.
	 */
	const FLAG_IMMUTABLE = 1;


	private $attributes;
	private $attributesMap;
	private $products;
	private $price;


	/**
	 * Initializes the order product instance.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param array $values Associative list of order product values
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface[] $attributes List of order attribute items
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered subproduct items
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [], array $products = [] )
	{
		parent::__construct( 'order.base.product.', $values );

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $attributes );
		$this->attributes = $attributes;

		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $products );
		$this->products = $products;

		$this->price = $price;
	}


	/**
	 * Clones internal objects of the order base product item.
	 */
	public function __clone()
	{
		$this->price = clone $this->price;
	}


	/**
	 * Returns the value or list of values of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return array|string|null Value or list of values of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( $code, $type = '' )
	{
		$map = $this->getAttributeMap();

		if( !isset( $map[$type][$code] ) ) {
			return null;
		}

		if( count( $map[$type][$code] ) === 1 ) {
			return reset( $map[$type][$code] )->getValue();
		}

		$list = [];
		foreach( $map[$type][$code] as $item ) {
			$list[] = $item->getValue();
		}

		return $list;
	}


	/**
	 * Returns the attribute item or list of attribute items for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface|array|null
	 * 	Attribute item or list of items for the ordered product and the given code
	 */
	public function getAttributeItem( $code, $type = '' )
	{
		$map = $this->getAttributeMap();

		if( !isset( $map[$type][$code] ) ) {
			return null;
		}

		if( count( $map[$type][$code] ) === 1 ) {
			return reset( $map[$type][$code] );
		}

		return $map[$type][$code];
	}


	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function getAttributeItems( $type = null )
	{
		if( $type === null ) {
			return $this->attributes;
		}

		$list = [];

		foreach( $this->attributes as $attrItem )
		{
			if( $attrItem->getType() === $type ) {
				$list[] = $attrItem;
			}
		}

		return $list;
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface $item )
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
	 * Sets the new list of attribute items for the product.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItems( array $attributes )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $attributes );

		$this->attributes = $attributes;
		$this->attributesMap = null;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the price item for the product.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate
	 */
	public function getPrice()
	{
		return $this->price;
	}


	/**
	 * Sets the price item for the product.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item containing price and additional costs
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setPrice( \Aimeos\MShop\Price\Item\Iface $price )
	{
		if( $price !== $this->price )
		{
			$this->price = $price;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns all of sub-product items
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] List of product items
	 */
	public function getProducts()
	{
		return $this->products;
	}


	/**
	 * Sets all sub-product items
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of product items
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setProducts( array $products )
	{
		\Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $products );

		$this->products = $products;
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
		return 'order/base/product';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );
		$price = $item->getPrice();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.product.price': $price = $price->setValue( $value ); break;
				case 'order.base.product.costs': $price = $price->setCosts( $value ); break;
				case 'order.base.product.rebate': $price = $price->setRebate( $value ); break;
				case 'order.base.product.taxrate': $price = $price->setTaxRate( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item->setPrice( $price );
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['order.base.product.price'] = $this->price->getValue();
		$list['order.base.product.costs'] = $this->price->getCosts();
		$list['order.base.product.rebate'] = $this->price->getRebate();
		$list['order.base.product.taxrate'] = $this->price->getTaxRate();

		return $list;
	}


	/**
	 * Checks if the given flag constant is valid.
	 *
	 * @param integer $value Flag constant value
	 */
	protected function checkFlags( $value )
	{
		$value = (int) $value;

		if( $value < \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE ||
			$value > \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE
		) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}

		return $value;
	}


	/**
	 * Returns the attribute map for the ordered products.
	 *
	 * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface as value
	 */
	protected function getAttributeMap()
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
