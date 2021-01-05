<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Defines the number of the free "config" attributes for products with configurable options
 *
 * If customers can configure products using several options they can add and each option must be paid additionally,
 * this plugin recalculates the total product price based on the added options.
 *
 * Example:
 *  <attribute type> : <number of free options>
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
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( is_array( $value ) )
		{
			foreach( $value as $key => $product ) {
				$value[$key] = $this->updatePrice( $product );
			}
		}
		else
		{
			$value = $this->updatePrice( $value );
		}

		return $value;
	}


	/**
	 * Adds the prices of the attribute items without the given amount of free items
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Product price item
	 * @param array $attrItems Associative list of attribute IDs as keys and items with prices as values
	 * @param array $quantities Associative list of attribute IDs as keys and their quantities as values
	 * @param int $free Number of free items
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with attribute prices added
	 */
	protected function addPrices( \Aimeos\MShop\Price\Item\Iface $price, array $attrItems, array $quantities,
		int $free ) : \Aimeos\MShop\Price\Item\Iface
	{
		$priceManager = \Aimeos\MShop::create( $this->getContext(), 'price' );

		foreach( $attrItems as $attrId => $attrItem )
		{
			$prices = $attrItem->getRefItems( 'price', 'default', 'default' );

			if( !$prices->isEmpty() )
			{
				$qty = ( isset( $quantities[$attrId] ) ? $quantities[$attrId] : 0 );

				$quantity = ( $qty >= $free ? $qty - $free : 0 );
				$free = ( $free >= $qty ? $free - $qty : 0 );

				if( $quantity > 0 )
				{
					$priceItem = $priceManager->getLowestPrice( $prices, $quantity );
					$price = $price->addItem( $priceItem, $quantity );
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
	protected function getAttributeMap( array $ids ) : array
	{
		$attrMap = [];
		$attrManager = \Aimeos\MShop::create( $this->getContext(), 'attribute' );

		$search = $attrManager->filter()->slice( 0, count( $ids ) );
		$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );

		foreach( $attrManager->search( $search, ['price'] ) as $attrId => $attrItem ) {
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
	protected function sortByPrice( array $attrItems, array $attrQtys ) : array
	{
		$priceManager = \Aimeos\MShop::create( $this->getContext(), 'price' );

		$sortFcn = function( $a, $b ) use( $priceManager, $attrQtys )
		{
			if( ( $pricesA = $a->getRefItems( 'price', 'default', 'default' )->toArray() ) === [] ) {
				return 1;
			}

			if( ( $pricesB = $b->getRefItems( 'price', 'default', 'default' )->toArray() ) === [] ) {
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


	/** Updates the price of the product
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $product Ordered product for updating the price
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Ordered product with updated price
	 */
	protected function updatePrice( \Aimeos\MShop\Order\Item\Base\Product\Iface $product ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		$attrQtys = $attrTypes = [];
		$context = $this->getContext();
		$prodItem = \Aimeos\MShop::create( $context, 'product' )->get( $product->getProductId(), ['price'] );
		$prodConf = $prodItem->getConfig();


		foreach( $product->getAttributeItems( 'config' ) as $attr )
		{
			$attrQtys[$attr->getAttributeId()] = $attr->getQuantity();
			$attrTypes[] = $attr->getCode();
		}

		if( array_intersect( $attrTypes, array_keys( $prodConf ) ) === [] ) {
			return $product;
		}


		$prices = $prodItem->getRefItems( 'price', 'default', 'default' );
		$priceItem = \Aimeos\MShop::create( $context, 'price' )->getLowestPrice( $prices, $product->getQuantity() );

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

		return $product->setPrice( $priceItem );
	}
}
