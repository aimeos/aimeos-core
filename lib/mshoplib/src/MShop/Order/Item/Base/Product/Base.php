<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
abstract class Base extends \Aimeos\MShop\Order\Item\Base implements Iface
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


	/**
	 * Initializes the order product instance.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item
	 * @param array $values Associative list of order product values
	 * @param array $attributes List of order attributes implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [] )
	{
		parent::__construct( 'order.base.product.', $values );

		\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface', $attributes );
		$this->attributes = $attributes;
	}


	/**
	 * Returns the value of the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return string|null value of the attribute item for the ordered product and the given code
	 */
	public function getAttribute( $code, $type = '' )
	{
		$map = $this->getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code]->getValue();
		}

		return null;
	}


	/**
	 * Returns the attribute item for the ordered product with the given code.
	 *
	 * @param string $code Code of the product attribute item
	 * @param string $type Type of the product attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface|null Attribute item for the ordered product and the given code
	 */
	public function getAttributeItem( $code, $type = '' )
	{
		$map = $this->getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code];
		}

		return null;
	}


	/**
	 * Returns the list of attribute items for the ordered product.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 */
	public function getAttributes( $type = null )
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

		if( !isset( $this->attributesMap[$type][$code] ) )
		{
			$this->attributesMap[$type][$code] = $item;
			$this->attributes[] = $item;
		}

		$this->attributesMap[$type][$code]->setValue( $item->getValue() );
		$this->setModified();

		return $this;
	}


	/**
	 * Sets the new list of attribute items for the product.
	 *
	 * @param array $attributes List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order base product item for chaining method calls
	 */
	public function setAttributes( array $attributes )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface', $attributes );

		$this->attributes = $attributes;
		$this->attributesMap = null;
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

			foreach( $this->attributes as $attribute ) {
				$this->attributesMap[$attribute->getType()][$attribute->getCode()] = $attribute;
			}
		}

		return $this->attributesMap;
	}
}
