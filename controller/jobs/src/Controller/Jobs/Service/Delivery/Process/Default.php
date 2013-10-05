<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
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
class Controller_Jobs_Service_Delivery_Process_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Process delivery services' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Sends paid orders to the ERP system or logistic partner' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();

		$days = $context->getConfig()->get( 'controller/jobs/service/delivery/process/limit-days', 90 );
		$date = date( 'Y-m-d 00:00:00', time() - 86400 * $days );

		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );
		$serviceSearch = $serviceManager->createSearch();
		$serviceSearch->setConditions( $serviceSearch->compare( '==', 'service.type.code', 'delivery' ) );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderSearch = $orderManager->createSearch();

		$start = 0;

		do
		{
			$serviceItems = $serviceManager->searchItems( $serviceSearch );

			foreach( $serviceItems as $serviceItem )
			{
				try
				{
					$serviceProvider = $serviceManager->getProvider( $serviceItem );

					$expr = array(
						$orderSearch->compare( '==', 'order.siteid', $serviceItem->getSiteId() ),
						$orderSearch->compare( '>', 'order.datepayment', $date ),
						$orderSearch->compare( '>', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_PENDING ),
						$orderSearch->compare( '==', 'order.statusdelivery', MShop_Order_Item_Abstract::STAT_UNFINISHED ),
						$orderSearch->compare( '==', 'order.base.service.code', $serviceItem->getCode() ),
						$orderSearch->compare( '==', 'order.base.service.type', 'delivery' ),
					);
					$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

					$orderStart = 0;

					do
					{
						$orderItems = $orderManager->searchItems( $orderSearch );

						foreach( $orderItems as $orderItem )
						{
							try
							{
								$serviceProvider->process( $orderItem );
								$orderManager->saveItem( $orderItem );
							}
							catch( Exception $e )
							{
								$str = 'Error while processing order with ID "%1$s": %2$s';
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
					$str = 'Error while processing service with ID "%1$s": %2$s';
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
