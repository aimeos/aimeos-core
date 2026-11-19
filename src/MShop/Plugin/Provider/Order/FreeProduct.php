<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
 * @package MShop
 * @subpackage Plugin
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds the configured subscription product to the basket for free
 *
 * Sets the price of the configured product to 0.00 and uses it's price as rebate for
 * the configured number of times. This is bound to the e-mail address of the customer.
 *
 * The following options are available:
 * - productcode: '...' (SKU code of the product that should be available for free)
 * - count: ... (how often the product can be bought for free)
 *
 * @package MShop
 * @subpackage Plugin
 */
class FreeProduct
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private array $beConfig = array(
		'productcode' => array(
			'code' => 'productcode',
			'internalcode' => 'productcode',
			'label' => 'SKU of the free product',
			'default' => '',
			'required' => true,
		),
		'count' => array(
			'code' => 'count',
			'internalcode' => 'count',
			'label' => 'Number of times the product is available for free',
			'type' => 'int',
			'default' => 1,
			'required' => true,
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MShop\Order\Item\Iface $p ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		$plugin = $this->object();

		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'setOrder.before' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * The free product is checked again before the order is stored because the
	 * basket of another session could have been stored after the free product
	 * has been added to the current basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If the product isn't available for free any more when storing the order
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		$code = $this->getConfigValue( 'productcode' );

		if( $action === 'addProduct.after' )
		{
			map( [$value] )->implements( \Aimeos\MShop\Order\Item\Product\Iface::class, true );

			if( $value->getProductCode() !== $code ) {
				return $value;
			}
		}

		$products = $order->getProducts()->filter( fn( $product ) => $product->getProductCode() === $code );

		if( $products->isEmpty() ) {
			return $value;
		}

		$qty = $products->sum( fn( $product ) => $product->getQuantity() );
		$address = current( $order->getAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT ) );
		$free = $address && $this->used( (string) $address->getEmail(), $code, $order ) + $qty <= (int) $this->getConfigValue( 'count' );
		$revoked = false;

		foreach( $products as $product )
		{
			$price = $product->getPrice();

			// prices are only lowered when products are added, not when the order is stored
			if( $free && $action === 'addProduct.after' && $price->getValue() > 0 ) {
				$product->setPrice( $price->setRebate( $price->getValue() )->setValue( '0.00' ) );
			} elseif( !$free && $this->isFree( $product ) ) {
				$product->setPrice( $price->setValue( $price->getRebate() )->setRebate( '0.00' ) );
				$revoked = true;
			}
		}

		if( $revoked && $action === 'setOrder.before' )
		{
			$msg = $this->context()->translate( 'mshop', 'Product "%1$s" is not available for free any more' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $code ), 409 );
		}

		return $value;
	}


	/**
	 * Tests if the product is free because of this plugin
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $product Ordered product
	 * @return bool TRUE if the product is free, FALSE if not
	 */
	protected function isFree( \Aimeos\MShop\Order\Item\Product\Iface $product ) : bool
	{
		$price = $product->getPrice();
		return $price->getValue() == 0 && $price->getRebate() > 0;
	}


	/**
	 * Returns the quantity of the free product in previous orders of the customer
	 *
	 * Besides confirmed orders, recently stored orders which are still unfinished
	 * are included because they may be confirmed later. Otherwise, the product
	 * could be ordered for free in several orders before the first one is confirmed.
	 *
	 * @param string $email E-mail address of the customer
	 * @param string $code Product code of the free product
	 * @param \Aimeos\MShop\Order\Item\Iface $order Current order which is excluded
	 * @return int Quantity of the product in previous orders
	 */
	protected function used( string $email, string $code, \Aimeos\MShop\Order\Item\Iface $order ) : int
	{
		$context = $this->context();

		// same time frame as in \Aimeos\MShop\Coupon\Provider\Base::redeemed()
		$hours = (int) $context->config()->get( 'mshop/coupon/provider/unfinished-hours', 24 );
		$manager = \Aimeos\MShop::create( $context, 'order' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.address.email', $email ),
			$search->compare( '==', 'order.address.type', \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT ),
			$search->compare( '==', 'order.product.prodcode', $code ),
			$search->or( [
				$search->compare( '>=', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING ),
				$search->and( [
					$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED ),
					$search->compare( '>=', 'order.ctime', date( 'Y-m-d H:i:s', time() - $hours * 3600 ) ),
				] ),
			] ),
		];

		if( ( $id = $order->getId() ) !== null ) {
			$expr[] = $search->compare( '!=', 'order.id', $id );
		}

		// @phpstan-ignore argument.type
		$result = $manager->aggregate( $search->add( $search->and( $expr ) ), 'order.address.email', 'order.product.quantity', 'sum' );

		return (int) ( $result[$email] ?? 0 );
	}
}
