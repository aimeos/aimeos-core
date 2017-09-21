<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH
 * @copyright Aimeos (aimeos.org), 2015-2017
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
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
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
		$p->addListener( $this->getObject(), 'deleteProduct.after' );
		$p->addListener( $this->getObject(), 'setAddress.after' );
		$p->addListener( $this->getObject(), 'deleteAddress.after' );
		$p->addListener( $this->getObject(), 'setService.after' );
		$p->addListener( $this->getObject(), 'deleteService.after' );
		$p->addListener( $this->getObject(), 'addCoupon.after' );
		$p->addListener( $this->getObject(), 'deleteCoupon.after' );
		$p->addListener( $this->getObject(), 'setOrder.before' );
		$p->addListener( $this->getObject(), 'check.after' );
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
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		$notAvailable = [];
		$couponManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'coupon' );

		foreach( $order->getCoupons() as $code => $products )
		{
			$search = $couponManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'coupon.code.code', $code ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, 1 );

			$results = $couponManager->searchItems( $search );

			if( ( $couponItem = reset( $results ) ) !== false )
			{
				$couponProvider = $couponManager->getProvider( $couponItem, $code );
				$couponProvider->updateCoupon( $order );
			}
			else
			{
				$notAvailable[$code] = 'coupon.gone';
			}
		}

		if( count( $notAvailable ) > 0 )
		{
			$codes = array( 'coupon' => $notAvailable );
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Coupon in basket is not available any more' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $codes );
		}

		return true;
	}

}
