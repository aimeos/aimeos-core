<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Order
 */


/**
 * Order delivery e-mail job controller.
 *
 * @package Controller
 * @subpackage Order
 */
class Controller_Jobs_Order_Email_Delivery_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Order delivery related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Sends order delivery status update e-mails' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$arcavias = $this->_getArcavias();
		$context = $this->_getContext();
		$config = $context->getConfig();
		$mailer = $context->getMail();
		$view = $context->getView();

		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$client = Client_Html_Email_Delivery_Factory::createClient( $context, $templatePaths );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		/** controller/jobs/order/email/delivery/default/limit-days
		 * Only send delivery e-mails of orders that were created in the past within the configured number of days
		 *
		 * The delivery e-mails are normally send immediately after the delivery
		 * status has changed. This option prevents e-mails for old order from
		 * being send in case anything went wrong or an update failed to avoid
		 * confusion of customers.
		 *
		 * @param integer Number of days
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/delivery/default/status
		 * @see controller/jobs/order/email/payment/default/limit-days
		 * @see controller/jobs/service/delivery/process/limit-days
		 */
		$limit = $config->get( 'controller/jobs/order/email/delivery/default/limit-days', 90 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		$default = array(
			MShop_Order_Item_Abstract::STAT_PROGRESS,
			MShop_Order_Item_Abstract::STAT_DISPATCHED,
			MShop_Order_Item_Abstract::STAT_REFUSED,
			MShop_Order_Item_Abstract::STAT_RETURNED,
		);

		/** controller/jobs/order/email/delivery/default/status
		 * Only send order delivery notification e-mails for these delivery status values
		 *
		 * Notification e-mail about delivery status changes can be sent for these
		 * status values:
		 * * 0: deleted
		 * * 1: pending
		 * * 2: progress
		 * * 3: dispatched
		 * * 4: delivered
		 * * 5: lost
		 * * 6: refused
		 * * 7: returned
		 *
		 * User-defined status values are possible but should be in the private
		 * block of values between 30000 and 32767.
		 *
		 * @param integer Delivery status constant
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/payment/default/status
		 * @see controller/jobs/order/email/delivery/default/limit-days
		 */
		foreach( (array) $config->get( 'controller/jobs/order/email/delivery/default/status', $default ) as $status )
		{
			$orderSearch = $orderManager->createSearch();

			$param = array( MShop_Order_Item_Status_Abstract::EMAIL_DELIVERY, $status );
			$orderFunc = $orderSearch->createFunction( 'order.containsStatus', $param );

			$expr = array(
				$orderSearch->compare( '>=', 'order.mtime', $limitDate ),
				$orderSearch->compare( '==', 'order.statusdelivery', $status ),
				$orderSearch->compare( '==', $orderFunc, 0 ),
			);
			$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

			$start = 0;

			do
			{
				$items = $orderManager->searchItems( $orderSearch );

				foreach( $items as $id => $item )
				{
					try
					{
						$orderBaseItem = $orderBaseManager->load( $item->getBaseId() );

						try {
							$addr = $orderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
						} catch( Exception $e ) {
							$addr = $orderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
						}

						$view->extAddressItem = $addr;
						$view->extOrderBaseItem = $orderBaseItem;
						$view->extOrderItem = $item;

						$helper = new MW_View_Helper_Translate_Default( $view, $context->getI18n( $addr->getLanguageId() ) );
						$view->addHelper( 'translate', $helper );

						$message = $mailer->createMessage();
						$helper = new MW_View_Helper_Mail_Default( $view, $message );
						$view->addHelper( 'mail', $helper );

						$client->setView( $view );
						$client->getHeader();
						$client->getBody();

						$mailer->send( $message );

						$statusItem = $orderStatusManager->createItem();
						$statusItem->setParentId( $id );
						$statusItem->setType( MShop_Order_Item_Status_Abstract::EMAIL_DELIVERY );
						$statusItem->setValue( $status );

						$orderStatusManager->saveItem( $statusItem );
					}
					catch( Exception $e )
					{
						$str = 'Error while trying to send delivery e-mail for order ID "%1$s" and status "%2$s": %3$s';
						$msg = sprintf( $str, $item->getId(), $item->getDeliveryStatus(), $e->getMessage() );
						$context->getLogger()->log( $msg );
					}
				}

				$count = count( $items );
				$start += $count;
				$orderSearch->setSlice( $start );
			}
			while( $count >= $orderSearch->getSliceSize() );
		}
	}
}
