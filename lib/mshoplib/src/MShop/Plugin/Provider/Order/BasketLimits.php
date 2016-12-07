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
 * Checks if ordered product sum and count of products is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class BasketLimits
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		if( !( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) ) {
			return true;
		}

		$context = $this->getContext();

		/** mshop/plugin/provider/order/complete/disable
		 * Disables the basket limits check
		 *
		 * If the BasketLimits plug-in is enabled, it enforces the configured
		 * limits before customers or anyone on behalf of them can continue the
		 * checkout process.
		 *
		 * This option enables e.g. call center agents to place orders which
		 * doesn't satisfy all requirements. It may be useful if you want to
		 * allow them to send free or replacements for lost or damaged products.
		 *
		 * @param boolean True to disable the check, false to keep it enabled
		 * @category Developer
		 * @category User
		 * @since 2014.03
		 */
		if( $context->getConfig()->get( 'mshop/plugin/provider/order/complete/disable', false ) ) {
			return true;
		}


		$count = 0;
		$sum = \Aimeos\MShop\Factory::createManager( $context, 'price' )->createItem();

		foreach( $order->getProducts() as $product )
		{
			$sum->addItem( $product->getPrice(), $product->getQuantity() );
			$count += $product->getQuantity();
		}

		$this->checkLimits( $sum, $count );

		return true;
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
	 * @param integer $count Total number of products in the basket
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimits( \Aimeos\MShop\Price\Item\Iface $sum, $count )
	{
		$config = $this->getItemBase()->getConfig();

		$this->checkLimitsValue( $config, $sum );
		$this->checkLimitsProducts( $config, $count );
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
	 * @param array $config Associative list of configuration key/value pairs
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimitsValue( array $config, \Aimeos\MShop\Price\Item\Iface $sum )
	{
		$currencyId = $sum->getCurrencyId();

		if( ( isset( $config['min-value'][$currencyId] ) ) && ( $sum->getValue() + $sum->getRebate() < $config['min-value'][$currencyId] ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The minimum basket value of %1$s isn\'t reached' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['min-value'][$currencyId] ) );
		}

		if( ( isset( $config['max-value'][$currencyId] ) ) && ( $sum->getValue() + $sum->getRebate() > $config['max-value'][$currencyId] ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum basket value of %1$s is exceeded' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['max-value'][$currencyId] ) );
		}
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 * @param integer $count Total number of products in the basket
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimitsProducts( array $config, $count )
	{
		if( ( isset( $config['min-products'] ) ) && ( $count < $config['min-products'] ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The minimum product quantity of %1$d isn\'t reached' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['min-products'] ) );
		}

		if( ( isset( $config['max-products'] ) ) && ( $count > $config['max-products'] ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'The maximum product quantity of %1$d is exceeded' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['max-products'] ) );
		}
	}
}
