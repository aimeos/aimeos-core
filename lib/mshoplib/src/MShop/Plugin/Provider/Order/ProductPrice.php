<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the products in a basket for changed prices
 *
 * Notifies the customers if a price of a product in the basket has changed in
 * the meantime. This plugin can handle the change from net to gross prices and
 * backwards if prices are recalculated for B2B or B2C customers. In these cases
 * the customer won't be notified.
 *
 * The following option is available:
 * - warn: Warn users by displaying a message in the basket if one or more prices
 *   has changed. This can be intentional if the price really has changed but will
 *   also be displayed if the customers get lower block/tier prices or custom prices
 *   after login
 * - ignore-modified: Set to true if all basket items with modified prices (e.g. by
 *   another plugin) should be excluded from the check. Uses the isModified() method
 *   of the item's price object.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductPrice
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'warn' => array(
			'code' => 'warn',
			'internalcode' => 'warn',
			'label' => 'Warn customers if price has changed',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false,
		),
		'ignore-modified' => array(
			'code' => 'ignore-modified',
			'internalcode' => 'ignore-modified',
			'label' => 'Ignore order items with a modified price (e.g. by another plugin)',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '1',
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$p->attach( $this->getObject(), 'check.after' );
		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) === 0 ) {
			return $value;
		}

		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		$attrIds = map();
		$prodCodes = $changedProducts = [];
		$orderProducts = $order->getProducts();

		foreach( $orderProducts as $pos => $item )
		{
			if( $item->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE
				|| $this->getConfigValue( 'ignore-modified' ) && $item->getPrice()->isModified()
			) {
				unset( $orderProducts[$pos] );
			}

			$attrIds->merge( $item->getAttributeItems()->getAttributeId()->toArray() );
			$prodCodes[] = $item->getProductCode();
		}


		$attributes = $this->getAttributeItems( $attrIds->unique()->toArray() );
		$prodMap = $this->getProductItems( $prodCodes );


		foreach( $orderProducts as $pos => $orderProduct )
		{
			if( !$prodMap->has( $orderProduct->getProductCode() )
				|| !$prodMap->get( $orderProduct->getProductCode() )->getRefItems( 'attribute', 'price', 'custom' )->isEmpty()
			) {
				continue; // Product isn't available or excluded
			}

			// fetch prices of articles/sub-products
			$refPrices = $prodMap->get( $orderProduct->getProductCode() )->getRefItems( 'price', 'default', 'default' );
			$price = $this->getPrice( $orderProduct, $refPrices, $attributes, $pos );

			if( $orderProduct->getPrice()->compare( $price ) === false )
			{
				$order->addProduct( $orderProduct->setPrice( $price ), $pos );
				$changedProducts[$pos] = 'price.changed';
			}
		}

		if( $this->getConfigValue( 'warn', false ) == true && count( $changedProducts ) > 0 )
		{
			$code = ['product' => $changedProducts];
			$msg = $this->getContext()->translate( 'mshop', 'Please have a look at the prices of the products in your basket' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return $value;
	}


	/**
	 * Returns the attribute items for the given IDs.
	 *
	 * @param array $list List of attribute IDs
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Attribute\Item\Iface
	 */
	protected function getAttributeItems( array $list ) : \Aimeos\Map
	{
		if( empty( $list ) ) {
			return map();
		}

		$attrManager = \Aimeos\MShop::create( $this->getContext(), 'attribute' );

		$search = $attrManager->filter( true )->slice( 0, count( $list ) );
		$search->setConditions( $search->and( [
			$search->compare( '==', 'attribute.id', $list ),
			$search->getConditions()
		] ) );

		return $attrManager->search( $search, ['price'] );
	}


	/**
	 * Returns the product items for the given product codes.
	 *
	 * @param string[] $prodCodes Product codes
	 * @return \Aimeos\Map Associative list of codes as keys and product items as values
	 */
	protected function getProductItems( array $prodCodes ) : \Aimeos\Map
	{
		if( empty( $prodCodes ) ) {
			return map();
		}

		$productManager = \Aimeos\MShop::create( $this->getContext(), 'product' );

		$search = $productManager->filter( true )->slice( 0, count( $prodCodes ) );
		$search->setConditions( $search->and( [
			$search->compare( '==', 'product.code', $prodCodes ),
			$search->getConditions(),
		] ) );

		return $productManager->search( $search, ['price', 'attribute' => ['custom']] )->col( null, 'product.code' );
	}


	/**
	 * Returns the actual price for the given order product.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct Ordered product
	 * @param \Aimeos\Map $refPrices Prices implementing \Aimeos\MShop\Price\Item\Iface and associated to the original product
	 * @param \Aimeos\Map $attributes Attribute items implementing \Aimeos\MShop\Attribute\Item\Iface with prices
	 * @param int $pos Position of the product in the basket
	 * @return \Aimeos\MShop\Price\Item\Iface Price item including the calculated price
	 */
	private function getPrice( \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct, \Aimeos\Map $refPrices,
		\Aimeos\Map $attributes, int $pos ) : \Aimeos\MShop\Price\Item\Iface
	{
		$context = $this->getContext();

		// fetch prices of selection/parent products
		if( $refPrices->isEmpty() )
		{
			$productManager = \Aimeos\MShop::create( $context, 'product' );
			$product = $productManager->get( $orderProduct->getProductId(), array( 'price' ) );
			$refPrices = $product->getRefItems( 'price', 'default', 'default' );
		}

		if( $refPrices->isEmpty() )
		{
			$pid = $orderProduct->getProductId();
			$pcode = $orderProduct->getProductCode();
			$codes = array( 'product' => array( $pos => 'product.price' ) );

			$msg = $this->getContext()->translate( 'mshop', 'No price for product ID "%1$s" or product code "%2$s" available' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $pid, $pcode ), -1, null, $codes );
		}

		$currency = $orderProduct->getPrice()->getCurrencyId();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$price = clone $priceManager->getLowestPrice( $refPrices, $orderProduct->getQuantity(), $currency );

		// add prices of product attributes to compute the end price for comparison
		foreach( $orderProduct->getAttributeItems() as $orderAttribute )
		{
			$attrItem = $attributes->get( $orderAttribute->getAttributeId() );
			$attrPrices = $attrItem ? $attrItem->getRefItems( 'price', 'default', 'default' ) : map();

			if( !$attrPrices->isEmpty() )
			{
				$lowPrice = $priceManager->getLowestPrice( $attrPrices, $orderAttribute->getQuantity() );
				$price = $price->addItem( $lowPrice, $orderAttribute->getQuantity() );
			}
		}

		// reset product rebates like in the basket controller
		return $price->setRebate( '0.00' );
	}
}
