<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Service;


/**
 * Order service item abstract class defining available types.
 *
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


	private ?array $attributesMap = null;
	private array $attrRmItems = [];


	/**
	 * Adds new and replaces existing attribute items for the service.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Service\Attribute\Iface[] $attributes List of order service attribute items
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function addAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		map( $attributes )->implements( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, true );

		foreach( $attributes as $attrItem ) {
			$this->setAttributeItem( $attrItem );
		}

		return $this;
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
	 * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface|array|null
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
	 * Returns the list of attribute items for the ordered service.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return \Aimeos\Map List of attribute items implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface
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
	 * Returns the deleted attribute items for the service.
	 *
	 * @return \Aimeos\Map List of items to be removed
	 */
	public function getAttributeItemsDeleted() : \Aimeos\Map
	{
		return map( $this->attrRmItems );
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Attribute\Iface $item Service attribute item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItem( \Aimeos\MShop\Order\Item\Service\Attribute\Iface $item ) : \Aimeos\MShop\Order\Item\Service\Iface
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

		return $this->setModified();
	}


	/**
	 * Sets the new list of attribute items for the service.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Order\Item\Service\Attribute\Iface[] $attributes List of order service attribute items
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setAttributeItems( iterable $attributes ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		map( $attributes )->implements( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, true );

		$this->attrRmItems = map( $this->get( '.attributes', [] ) )
			->diff( $attributes )
			->merge( $this->attrRmItems )
			->unique()
			->toArray();

		$this->set( '.attributes', iterator_to_array( $attributes ) );
		$this->attributesMap = null;

		return $this;
	}


	/**
	 * Adds a new transaction to the service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item Transaction item
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function addTransaction( \Aimeos\MShop\Order\Item\Service\Transaction\Iface $item ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( '.transactions', map( $this->get( '.transactions', [] ) )->push( $item ) );
	}


	/**
	 * Returns the list of transactions items for the service.
	 *
	 * @param string|null $type Filters returned transactions by the given type or null for no filtering
	 * @return \Aimeos\Map List of transaction items implementing \Aimeos\MShop\Order\Item\Service\Attribute\Iface
	 */
	public function getTransactions( ?string $type = null ) : \Aimeos\Map
	{
		return map( $this->get( '.transactions', [] ) );
	}


	/**
	 * Sets the new list of transactions items for the service.
	 *
	 * @param iterable $list List of order service transaction items
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order base service item for chaining method calls
	 */
	public function setTransactions( iterable $list ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $this->set( '.transactions', $list );
	}


	/**
	 * Returns the attribute map for the ordered services.
	 *
	 * @return array Associative list of type and code as key and an \Aimeos\MShop\Order\Item\Service\Attribute\Iface as value
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
