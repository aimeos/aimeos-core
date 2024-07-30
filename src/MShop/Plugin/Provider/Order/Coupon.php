<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Updates the basket depending on the coupon
 *
 * Executes the coupon providers again on any basket change so they can update
 * the basket. This is necessary if either the requirement for coupons aren't
 * met any more or for updating percentual rebates.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Coupon
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
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
		$p->attach( $plugin, 'deleteProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );
		$p->attach( $plugin, 'addAddress.after' );
		$p->attach( $plugin, 'deleteAddress.after' );
		$p->attach( $plugin, 'setAddresses.after' );
		$p->attach( $plugin, 'addService.after' );
		$p->attach( $plugin, 'deleteService.after' );
		$p->attach( $plugin, 'setServices.after' );
		$p->attach( $plugin, 'addCoupon.after' );
		$p->attach( $plugin, 'deleteCoupon.after' );
		$p->attach( $plugin, 'setOrder.before' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		$notAvailable = false;
		$context = $this->context();

		$manager = \Aimeos\MShop::create( $context, 'coupon' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		foreach( $order->getCoupons() as $code => $products )
		{
			$search = $manager->filter( true )
				->add( 'coupon.code.code', '==', $code )
				->add( $codeManager->filter( true )->getConditions() )
				->slice( 0, 1 );

			if( ( $item = $manager->search( $search )->first() ) !== null ) {
				$manager->getProvider( $item, $code )->update( $order );
			} else {
				$order->deleteCoupon( $code );
				$notAvailable = true;
			}
		}

		if( $notAvailable )
		{
			$msg = $this->context()->translate( 'mshop', 'Coupon is not available any more' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg );
		}

		return $value;
	}

}
