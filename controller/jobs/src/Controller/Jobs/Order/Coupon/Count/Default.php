<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order coupon job controller for decreasing coupon counts.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Coupon_Count_Default
	extends Controller_Jobs_Abstract
	implements Controller_Jobs_Interface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Order coupon counts' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Decreases the counts of successfully redeemed coupons' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		$orderCouponManager = $orderManager->getSubManager( 'base' )->getSubManager( 'coupon' );
		$codeManager = MShop_Coupon_Manager_Factory::createManager( $context )->getSubManager( 'code' );


		$statusItem = $orderStatusManager->createItem();
		$statusItem->setType( MShop_Order_Item_Status_Abstract::COUPON_UPDATE );
		$statusItem->setValue( 1 );


		$search = $orderCouponManager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		$criteria = $orderManager->createSearch();

		$params = array( MShop_Order_Item_Status_Abstract::COUPON_UPDATE, 1 );
		$cmpfunc = $criteria->createFunction( 'order.containsStatus', $params );

		$expr = array(
			$criteria->compare( '>=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED ),
			$criteria->compare( '==', $cmpfunc, 0 ),
		);
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );


		$start = 0;

		do
		{
			$baseIds = array();
			$items = $orderManager->searchItems( $criteria );

			foreach( $items as $id => $item ) {
				$baseIds[ $item->getBaseId() ][] = $id;
			}

			$search->setConditions( $search->compare( '==', 'order.base.coupon.baseid', array_keys( $baseIds ) ) );

			foreach( $orderCouponManager->searchItems( $search ) as $couponItem )
			{
				try
				{
					foreach( (array) $baseIds[ $couponItem->getBaseId() ] as $orderId )
					{
						$codeManager->decrease( $couponItem->getCode(), 1 );

						$statusItem->setId( null );
						$statusItem->setParentId( $orderId );
						$orderStatusManager->saveItem( $statusItem );
					}
				}
				catch( Exception $e )
				{
					$str = 'Error while updating coupons for order ID "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $id, $e->getMessage() ) );
				}
			}

			$count = count( $items );
			$start += $count;
			$criteria->setSlice( $start );
		}
		while( $count >= $criteria->getSliceSize() );
	}
}
