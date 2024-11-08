<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Product;


/**
 * Order product item abstract class defining available flags.
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


	private ?array $attributesMap = null;


	/**
	 * Adds new and replaces existing attribute items for the product.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function addAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		map( $attributes )->implements( \Aimeos\MShop\Order\Item\Product\Attribute\Iface::class, true );

		foreach( $attributes as $attrItem ) {
			$this->setAttributeItem( $attrItem );
		}

		return $this;
	}


	/**
	 * Returns the value or list of values of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type or list of types of the product attribute items
	 * @return array|string|null Value or list of values of the attribute item for the ordered product and the given code
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
	 * Returns the attribute item or list of attribute items for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param array|string $type Type of the product attribute item
	 * @return \Aimeos\MShop\Order\Item\Product\Attribute\Iface|array|null
	 * 	Attribute item or list of items for the ordered product and the given code
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
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Product\Attribute\Iface
	 */
	public function getAttributeItems( ?string $type = null ) : \Aimeos\Map
	{
		if( $type === null ) {
			return map( $this->get( '.attributes', [] ) );
		}

		$list = [];

		foreach( $this->get( '.attributes', [] ) as $attrItem )
		{
			if( $attrItem->getType() === $type ) {
				$list[] = $attrItem;
			}
		}

		return map( $list );
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Product\Attribute\Iface $item ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		$this->getAttributeMap();

		$type = $item->getType();
		$code = $item->getCode();
		$attrId = $item->getAttributeId();

		if( !isset( $this->attributesMap[$type][$code][$attrId] ) )
		{
			$this->set( '.attributes', map( $this->get( '.attributes', [] ) )->push( $item ) );
			$this->attributesMap[$type][$code][$attrId] = $item;
		}

		$this->attributesMap[$type][$code][$attrId]->setValue( $item->getValue() );
		$this->setModified();

		return $this;
	}


	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Product\Attribute\Iface[] $attributes List of order product attribute items
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		( $attributes = map( $attributes ) )->implements( \Aimeos\MShop\Order\Item\Product\Attribute\Iface::class, true );

		$this->set( '.attributes', $attributes );
		$this->attributesMap = null;

		return $this;
	}


	/**
	 * Checks if the given flag constant is valid.
	 *
	 * @param int $value Flag constant value
	 */
	protected function checkFlags( int $value )
	{
		if( $value < \Aimeos\MShop\Order\Item\Product\Base::FLAG_NONE ||
			$value > \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE
		) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags "%1$s" not within allowed range', $value ) );
		}

		return $value;
	}


	/**
	 * Returns the attribute map for the ordered products.
	 *
	 * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Product\Attribute\Iface as value
	 */
	protected function getAttributeMap() : array
	{
		if( !isset( $this->attributesMap ) )
		{
			$this->attributesMap = [];

			foreach( $this->get( '.attributes', [] ) as $item ) {
				$this->attributesMap[$item->getType()][$item->getCode()][$item->getAttributeId()] = $item;
			}
		}

		return $this->attributesMap;
	}
}
