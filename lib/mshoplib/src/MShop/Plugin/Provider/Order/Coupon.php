<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH
 * @copyright Aimeos (aimeos.org), 2015-2018
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
 *	madmin/log/manager/standard/loglevel = 7
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
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$plugin = $this->getObject();

		$p->addListener( $plugin, 'addProduct.after' );
		$p->addListener( $plugin, 'deleteProduct.after' );
		$p->addListener( $plugin, 'setProducts.after' );
		$p->addListener( $plugin, 'deleteAddress.after' );
		$p->addListener( $plugin, 'setAddress.after' );
		$p->addListener( $plugin, 'setAddresses.after' );
		$p->addListener( $plugin, 'addService.after' );
		$p->addListener( $plugin, 'deleteService.after' );
		$p->addListener( $plugin, 'setServices.after' );
		$p->addListener( $plugin, 'addCoupon.after' );
		$p->addListener( $plugin, 'deleteCoupon.after' );

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
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		$notAvailable = [];
		$context = $this->getContext();

		$manager = \Aimeos\MShop::create( $context, 'coupon' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		foreach( $order->getCoupons() as $code => $products )
		{
			$search = $manager->createSearch( true )->setSlice( 0, 1 );
			$expr = array(
				$search->compare( '==', 'coupon.code.code', $code ),
				$codeManager->createSearch( true )->getConditions(),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$items = $manager->searchItems( $search );

			if( ( $item = reset( $items ) ) !== false ) {
				$manager->getProvider( $item, $code )->update( $order );
			} else {
				$notAvailable[$code] = 'coupon.gone';
			}
		}

		if( count( $notAvailable ) > 0 )
		{
			$codes = array( 'coupon' => $notAvailable );
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Coupon in basket is not available any more' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $codes );
		}

		return $value;
	}

}
