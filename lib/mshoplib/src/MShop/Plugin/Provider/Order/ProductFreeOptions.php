<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Configurable free product options
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductFreeOptions
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'addProduct.after' );
		$p->addListener( $this->getObject(), 'editProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $order );

		$attrQtys = $attrTypes = [];
		$context = $this->getContext();

		$prodManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$prodItem = $prodManager->getItem( $value->getProductId(), ['price'] );
		$prodConf = $prodItem->getConfig();


		foreach( $value->getAttributes( 'config' ) as $attr )
		{
			$attrQtys[$attr->getAttributeId()] = $attr->getQuantity();
			$attrTypes[] = $attr->getCode();
		}

		if( array_intersect( $attrTypes, array_keys( $prodConf ) ) === [] ) {
			return true;
		}


		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );

		$prices = $prodItem->getRefItems( 'price', 'default', 'default' );
		$priceItem = $priceManager->getLowestPrice( $prices, $value->getQuantity() );

		foreach( $this->getAttributeMap( array_keys( $attrQtys ) ) as $type => $list )
		{
			if( isset( $prodConf[$type] ) )
			{
				$list = $this->sortByPrice( $list, $attrQtys );
				$priceItem = $this->addPrices( $priceItem, $list, $attrQtys, (int) $prodConf[$type] );
			}
			else
			{
				$priceItem = $this->addPrices( $priceItem, $list, $attrQtys, 0 );
			}
		}

		$value->setPrice( $priceItem );

		return true;
	}


	/**
	 * Adds the prices of the attribute items without the given amount of free items
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Product price item
	 * @param array $attrItems Associative list of attribute IDs as keys and items with prices as values
	 * @param array $quantities Associative list of attribute IDs as keys and their quantities as values
	 * @param integer Number of free items
	 * @param \Aimeos\MShop\Price\Item\Iface Price item with attribute prices added
	 */
	protected function addPrices( \Aimeos\MShop\Price\Item\Iface $price, array $attrItems, array $quantities, $free )
	{
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );

		foreach( $attrItems as $attrId => $attrItem )
		{
			if( ( $prices = $attrItem->getRefItems( 'price', 'default', 'default' ) ) !== [] )
			{
				$qty = ( isset( $quantities[$attrId] ) ? $quantities[$attrId] : 0 );

				$quantity = ( $qty >= $free ? $qty - $free : 0 );
				$free = ( $free >= $qty ? $free - $qty : 0 );

				if( $quantity > 0 )
				{
					$priceItem = $priceManager->getLowestPrice( $prices, $quantity );
					$price->addItem( $priceItem, $quantity );
				}
			}
		}

		return $price;
	}


	/**
	 * Returns the attribute items including the prices for the given IDs
	 *
	 * @param array $ids List of attribute IDs
	 * @return array Associative List of attribute type and ID as keys and \Aimeos\MShop\Attribute\Item\Iface as values
	 */
	protected function getAttributeMap( array $ids )
	{
		$attrMap = [];
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );

		$search = $attrManager->createSearch()->setSlice( 0, count( $ids ) );
		$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );

		foreach( $attrManager->searchItems( $search, ['price'] ) as $attrId => $attrItem ) {
			$attrMap[$attrItem->getType()][$attrId] = $attrItem;
		}

		return $attrMap;
	}


	/**
	 * Sorts the given attribute items by their price (lowest first)
	 *
	 * @param \Aimeos\MShop\Attribute\Item\Iface[] $attrItems Associative list of attribute IDs as keys and items as values
	 * @param array $attrQtys Associative list of attribute IDs as keys and their quantities as values
	 * @return \Aimeos\MShop\Attribute\Item\Iface[] Sorted associative list of attribute IDs as keys and items as values
	 */
	protected function sortByPrice( array $attrItems, array $attrQtys )
	{
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );

		$sortFcn = function( $a, $b ) use( $priceManager, $attrQtys )
		{
			if( ( $pricesA = $a->getRefItems( 'price', 'default', 'default' ) ) === [] ) {
				return 1;
			}

			if( ( $pricesB = $b->getRefItems( 'price', 'default', 'default' ) ) === [] ) {
				return -1;
			}

			$qty = ( isset( $attrQtys[$a->getId()] ) ? $attrQtys[$a->getId()] : 0 );
			$p1 = $priceManager->getLowestPrice( $pricesA, $qty );

			$qty = ( isset( $attrQtys[$b->getId()] ) ? $attrQtys[$b->getId()] : 0 );
			$p2 = $priceManager->getLowestPrice( $pricesB, $qty );

			if( $p1->getValue() < $p2->getValue() ) {
				return -1;
			} elseif( $p1->getValue() > $p2->getValue() ) {
				return 1;
			}

			return 0;
		};

		uasort( $attrItems, $sortFcn );

		return $attrItems;
	}
}
