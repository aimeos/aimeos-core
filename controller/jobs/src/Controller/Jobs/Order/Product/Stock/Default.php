<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Decreases the stock levels of completed orders.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Product_Stock_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Order product stock levels' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Decreases the stock levels of products in completed orders' );
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
		$orderProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$stockManager = MShop_Product_Manager_Factory::createManager( $context )->getSubManager( 'stock' );

		$search = $orderProductManager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		$criteria = $orderManager->createSearch();

		$params = array( MShop_Order_Item_Status_Abstract::STOCK_UPDATE, 1 );
		$cmpfunc = $criteria->createFunction( 'order.containsStatus', $params );

		$expr = array(
			$criteria->compare( '>=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED ),
			$criteria->compare( '==', $cmpfunc, 0 ),
		);
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		/** @todo Repository configuration in sub-sites? */
		$siteConfig = $context->getLocale()->getSite()->getConfig();
		$repository = ( isset( $siteConfig['repository'] ) ? $siteConfig['repository'] : 'default' );

		$start = 0;

		do
		{
			$items = $orderManager->searchItems( $criteria );

			foreach( $items as $id => $item )
			{
				try
				{
					$expr = $search->compare( '==', 'order.base.product.baseid', $item->getBaseId() );
					$search->setConditions( $expr );

					foreach( $orderProductManager->searchItems( $search ) as $proditem ) {
						$stockManager->decrease( $proditem->getProductCode(), $repository, $proditem->getQuantity() );
					}

					$statusItem = $orderStatusManager->createItem();
					$statusItem->setParentId( $id );
					$statusItem->setType( MShop_Order_Item_Status_Abstract::STOCK_UPDATE );
					$statusItem->setValue( 1 );
					$orderStatusManager->saveItem( $statusItem );
				}
				catch( Exception $e )
				{
					$str = 'Error while updating stock for order ID "%1$s": %2$s';
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
