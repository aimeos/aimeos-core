<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Order cleanup job controller for removing unfinished orders.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Cleanup_Unfinished_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Removes unfinished orders' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Deletes unfinished orders an makes their products and coupon codes available again' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->_getContext();
		$controller = Controller_Common_Order_Factory::createController( $context );
		$baseManager = MShop_Factory::createManager( $context, 'order/base' );
		$manager = MShop_Factory::createManager( $context, 'order' );

		/** controller/jobs/order/cleanup/unfinished/keep-hours
		 * Release the ordered products after the configured time if no payment was confirmed
		 *
		 * After a customer creates an order and before he is redirected to the
		 * payment provider (if necessary), the ordered products, coupon codes,
		 * etc. are blocked for that customer. Normally, they should be released
		 * a certain amount of time if no payment confirmation arrives so
		 * customers can order the products and use the coupon codes again.
		 *
		 * The configured number of hours should be high enough to avoid releasing
		 * products and coupon codes in case of temporary technical problems!
		 *
		 * The unfinished orders are deleted afterwards to keep the database clean.
		 *
		 * @param integer Number of hours
		 * @since 2014.07
		 * @category User
		 */
		$hours = $context->getConfig()->get( 'controller/jobs/order/cleanup/unfinished/keep-hours', 24 );
		$limit = date( 'Y-m-d H:i:s', time() - 3600 * $hours );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '<', 'order.mtime', $limit ),
			$search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_UNFINISHED ),
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
