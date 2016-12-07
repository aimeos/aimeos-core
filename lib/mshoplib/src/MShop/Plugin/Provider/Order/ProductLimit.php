<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Product limit implementation if count or sum of a single or of all products in an order exceeds given limit
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductLimit
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		$this->checkWithoutCurrency( $order, $value );
		$this->checkWithCurrency( $order, $value );

		return true;
	}


	/**
	 * Checks for the product limits when the configuration doesn't contain limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $value Order product item
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithoutCurrency( \Aimeos\MShop\Order\Item\Base\Iface $order,
		\Aimeos\MShop\Order\Item\Base\Product\Iface $value )
	{
		$config = $this->getItemBase()->getConfig();

		if( isset( $config['single-number-max'] ) && !is_array( $config['single-number-max'] )
			&& $value->getQuantity() > (int) $config['single-number-max']
		) {
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum product quantity is %1$d' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['single-number-max'] ) );
		}


		if( isset( $config['total-number-max'] ) && !is_array( $config['total-number-max'] ) )
		{
			$total = $value->getQuantity();

			foreach( $order->getProducts() as $product ) {
				$total += $product->getQuantity();
			}

			if( $total > (int) $config['total-number-max'] )
			{
				$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum quantity of all products is %1$d' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['total-number-max'] ) );
			}
		}
	}


	/**
	 * Checks for the product limits when the configuration contains limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $value Order product item
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithCurrency( \Aimeos\MShop\Order\Item\Base\Iface $order,
		\Aimeos\MShop\Order\Item\Base\Product\Iface $value )
	{
		$config = $this->getItemBase()->getConfig();
		$currencyId = $value->getPrice()->getCurrencyId();

		if( isset( $config['single-value-max'][$currencyId] )
			&& $value->getPrice()->getValue() * $value->getQuantity() > (float) $config['single-value-max'][$currencyId]
		) {
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum product value is %1$s' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['single-value-max'][$currencyId] ) );
		}


		if( isset( $config['total-value-max'][$currencyId] ) )
		{
			$price = clone $value->getPrice();
			$price->setValue( $price->getValue() * $value->getQuantity() );

			foreach( $order->getProducts() as $product ) {
				$price->addItem( $product->getPrice(), $product->getQuantity() );
			}

			if( (float) $price->getValue() > (float) $config['total-value-max'][$currencyId] )
			{
				$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum value of all products is %1$s' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['total-value-max'][$currencyId] ) );
			}
		}
	}
}
