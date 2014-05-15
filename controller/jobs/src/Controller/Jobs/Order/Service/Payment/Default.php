<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Sends paid orders to the ERP system or logistic partner.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Service_Payment_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Capture authorized payments' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Authorized payments of orders will be captured after dispatching or after a configurable amount of time' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();
		$config = $context->getConfig();


		/** controller/jobs/order/service/payment/limit-days
		 * Only start capturing payments of orders that were created in the past within the configured number of days
		 *
		 * Capturing payments is normally done immediately after the delivery
		 * status changed to "dispatched" or "delivered". This option prevents
		 * payments from being captured in case anything went wrong and payments
		 * of old orders would be captured now.
		 *
		 * @param integer Number of days
		 * @since 2014.07
		 * @category User
		 * @category Developer
		 */
		$days = $config->get( 'controller/jobs/order/service/payment/limit-days', 90 );
		$date = date( 'Y-m-d 00:00:00', time() - 86400 * $days );

		/** controller/jobs/order/service/payment/capture-days
		 * Automatically capture payments after the configured amount of days
		 *
		 * You can capture authorized payments after a configured amount of days
		 * even if the parcel for the order wasn't dispatched yet. This is useful
		 * for payment methods like credit cards where autorizations are revoked
		 * by the aquirers after some time (usually seven days).
		 *
		 * @param integer Number of days
		 * @since 2014.07
		 * @category User
		 * @category Developer
		 */
		if( ( $capDays = $config->get( 'controller/jobs/order/service/payment/capture-days' ) ) !== null ) {
			$capDate = date( 'Y-m-d 00:00:00', time() - 86400 * $capDays );
		}


		$serviceManager = MShop_Factory::createManager( $context, 'service' );
		$serviceSearch = $serviceManager->createSearch();
		$serviceSearch->setConditions( $serviceSearch->compare( '==', 'service.type.code', 'payment' ) );

		$orderManager = MShop_Factory::createManager( $context, 'order' );
		$orderSearch = $orderManager->createSearch();

		$status = array( MShop_Order_Item_Abstract::STAT_DISPATCHED, MShop_Order_Item_Abstract::STAT_DELIVERED );
		$start = 0;

		do
		{
			$serviceItems = $serviceManager->searchItems( $serviceSearch );

			foreach( $serviceItems as $serviceItem )
			{
				try
				{
					$serviceProvider = $serviceManager->getProvider( $serviceItem );

					if( !$serviceProvider->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) ) {
						continue;
					}


					$expr = array();
					$expr[] = $orderSearch->compare( '==', 'order.siteid', $serviceItem->getSiteId() );
					$expr[] = $orderSearch->compare( '>', 'order.datepayment', $date );

					if( $capDays !== null ) {
						$expr[] = $orderSearch->compare( '<=', 'order.datepayment', $capDate );
					}

					$expr[] = $orderSearch->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED );

					if( $capDays === null ) {
						$expr[] = $orderSearch->compare( '==', 'order.statusdelivery', $status );
					}

					$expr[] = $orderSearch->compare( '==', 'order.base.service.code', $serviceItem->getCode() );
					$expr[] = $orderSearch->compare( '==', 'order.base.service.type', 'payment' );

					$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );


					$orderStart = 0;

					do
					{
						$orderItems = $orderManager->searchItems( $orderSearch );

						foreach( $orderItems as $orderItem )
						{
							try
							{
								$serviceProvider->capture( $orderItem );
								$orderManager->saveItem( $orderItem );
							}
							catch( Exception $e )
							{
								$str = 'Error while capturing payment for order with ID "%1$s": %2$s';
								$context->getLogger()->log( sprintf( $str, $orderItem->getId(), $e->getMessage() ) );
							}
						}

						$orderCount = count( $orderItems );
						$orderStart += $orderCount;
						$orderSearch->setSlice( $orderStart );
					}
					while( $orderCount >= $orderSearch->getSliceSize() );
				}
				catch( Exception $e )
				{
					$str = 'Error while capturing payments for service with ID "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $serviceItem->getId(), $e->getMessage() ) );
				}
			}

			$count = count( $serviceItems );
			$start += $count;
			$serviceSearch->setSlice( $start );
		}
		while( $count >= $serviceSearch->getSliceSize() );
	}
}
