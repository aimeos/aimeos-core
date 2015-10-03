<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order cleanup job controller for removing unpaid orders.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Cleanup_Unpaid_Default
	extends Controller_Jobs_Base
	implements Controller_Jobs_Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Removes unpaid orders' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Deletes unpaid orders to keep the database clean' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$controller = Controller_Common_Order_Factory::createController( $context );
		$baseManager = MShop_Factory::createManager( $context, 'order/base' );
		$manager = MShop_Factory::createManager( $context, 'order' );

		/** controller/jobs/order/cleanup/unpaid/keep-days
		 * Removes all orders from the database that are unpaid
		 *
		 * Orders with a payment status of deleted, canceled or refused are only
		 * necessary for the records for a certain amount of time. Afterwards,
		 * they can be deleted from the database most of the time.
		 *
		 * The number of days should be high enough to ensure that you keep the
		 * orders as long as your customers will be asking what happend to their
		 * orders.
		 *
		 * @param integer Number of days
		 * @since 2014.07
		 * @category User
		 */
		$days = $context->getConfig()->get( 'controller/jobs/order/cleanup/unpaid/keep-days', 3 );
		$limit = date( 'Y-m-d H:i:s', time() - 86400 * $days );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '<', 'order.mtime', $limit ),
			$search->compare( '<', 'order.statuspayment', MShop_Order_Item_Base::PAY_REFUND ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$start = 0;

		do
		{
			$baseIds = array();
			$items = $manager->searchItems( $search );

			foreach( $items as $item )
			{
				$controller->unblock( $item );
				$baseIds[] = $item->getBaseId();
			}

			$baseManager->deleteItems( $baseIds );

			$count = count( $items );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );
	}
}
