<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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


	/**
	 * Initializes the order base service item
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price
	 * @param array $values Values to be set on initialisation
	 * @param array $attributes Attributes to be set on initialisation
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, array $values = [], array $attributes = [] )
	{
		parent::__construct( 'order.base.service.', $values );

		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Attribute\\Iface', $attributes );
		$this->attributes = $attributes;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'order/base/service';
	}


	/**
	 * Returns the value or list of values of the attribute item for the ordered service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return array|string|null Value or list of values of the attribute item for the ordered service and the given code
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
	 * Returns the attribute item or list of attribute items for the ordered service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface|array|null
	 * 	Attribute item or list of items for the ordered service and the given code
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
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface $item )
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
	 * @return array List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
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
	 * Sets the new list of attribute items for the service.
	 *
	 * @param array $attributes List of attribute items implementing \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItems( array $attributes )
	{
		\Aimeos\MW\Common\Base::checkClassList( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Attribute\\Iface', $attributes );

		$this->attributes = $attributes;
		$this->attributesMap = null;
		$this->setModified();

		return $this;
	}


	/*
	 * @deprecated Use getAttributeItems() instead
	 */
	public function getAttributes( $type = null )
	{
		return $this->getAttributeItems( $type );
	}


	/*
	 * @deprecated Use setAttributeItems() instead
	 */
	public function setAttributes( array $attributes )
	{
		return $this->setAttributeItems( $attributes );
	}


	/**
	 * Checks if the given address type is valid
	 *
	 * @param string $value Address type defined in \Aimeos\MShop\Order\Item\Base\Address\Base
	 * @throws \Aimeos\MShop\Order\Exception If type is invalid
	 */
	protected function checkType( $value )
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
