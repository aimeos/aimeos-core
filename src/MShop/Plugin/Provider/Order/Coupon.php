<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH
 * @copyright Aimeos (aimeos.org), 2015-2022
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
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
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
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		map( [$order] )->implements( \Aimeos\MShop\Order\Item\Base\Iface::class, true );

		$notAvailable = false;
		$context = $this->context();

		$manager = \Aimeos\MShop::create( $context, 'coupon' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		foreach( $order->getCoupons() as $code => $products )
		{
			$search = $manager->filter( true )->slice( 0, 1 );
			$expr = array(
				$search->compare( '==', 'coupon.code.code', $code ),
				$codeManager->filter( true )->getConditions(),
				$search->getConditions(),
			);
			$search->setConditions( $search->and( $expr ) );

			if( ( $item = $manager->search( $search )->first() ) !== null && $this->available( (string) $code, $order ) ) {
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


	/**
	 * Tests if the coupon code count isn't exhausted by unfinished orders
	 *
	 * The coupon code counts are only decreased after the payment has been
	 * confirmed. Thus, recently stored but unfinished orders are subtracted from
	 * the count to prevent redeeming the same code in several orders before the
	 * first one is confirmed.
	 *
	 * @param string $code Coupon code entered by the customer
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket or order of the customer
	 * @return bool TRUE if the code can still be redeemed, FALSE if not
	 */
	protected function available( string $code, \Aimeos\MShop\Order\Item\Base\Iface $order ) : bool
	{
		$context = $this->context();
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		$filter = $codeManager->filter()->slice( 0, 1 );
		$filter->setConditions( $filter->compare( '==', 'coupon.code.code', $code ) );

		if( ( $codeItem = $codeManager->search( $filter )->first() ) === null || ( $count = $codeItem->getCount() ) === null ) {
			return true;
		}

		$manager = \Aimeos\MShop::create( $context, 'order' );
		$total = 0;

		$search = $manager->filter()->slice( 0, 1 );
		$expr = [
			$search->compare( '==', 'order.base.coupon.code', $code ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED ),
			$search->compare( '>=', 'order.ctime', $manager->unfinished() ),
		];

		if( ( $id = $order->getId() ) !== null ) {
			$expr[] = $search->compare( '!=', 'order.baseid', $id );
		}

		$search->setConditions( $search->and( $expr ) );
		$manager->search( $search, [], $total );

		return $count > $total;
	}

}
