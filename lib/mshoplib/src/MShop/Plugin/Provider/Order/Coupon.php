<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Update percent rebate value on change.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_Coupon
	extends MShop_Plugin_Provider_Factory_Base
	implements MShop_Plugin_Provider_Factory_Interface
{
	protected static $lock = false;


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
		$p->addListener( $this, 'deleteProduct.after' );
		$p->addListener( $this, 'setService.after' );
		$p->addListener( $this, 'addCoupon.after' );
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$notAvailable = array();

		if( self::$lock === false )
		{
			self::$lock = true;

			$couponManager = MShop_Factory::createManager( $this->getContext(), 'coupon' );

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

			self::$lock = false;
		}

		if( count( $notAvailable ) > 0 )
		{
			$codes = array( 'coupon' => $notAvailable );
			$msg = sprintf( 'Coupon in basket is not available any more' );
			throw new MShop_Plugin_Provider_Exception( $msg, -1, null, $codes );
		}

		return true;
	}

}
