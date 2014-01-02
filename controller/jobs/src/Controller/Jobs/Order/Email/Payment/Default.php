<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Order
 */


/**
 * Order payment e-mail job controller.
 *
 * @package Controller
 * @subpackage Order
 */
class Controller_Jobs_Order_Email_Payment_Default
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
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Order payment related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Sends order confirmation or payment status update e-mails' );
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

		$i18nPaths = $arcavias->getI18nPaths();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$client = Client_Html_Email_Payment_Factory::createClient( $context, $templatePaths );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$limit = $config->get( 'controller/jobs/order/email/payment/default/limit-days', 30 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		$default = array(
			MShop_Order_Item_Abstract::PAY_REFUND,
			MShop_Order_Item_Abstract::PAY_PENDING,
			MShop_Order_Item_Abstract::PAY_AUTHORIZED,
			MShop_Order_Item_Abstract::PAY_RECEIVED,
		);

		foreach( (array) $config->get( 'controller/jobs/order/email/payment/default/status', $default ) as $status )
		{
			$orderSearch = $orderManager->createSearch();

			$param = array( MShop_Order_Item_Status_Abstract::EMAIL_PAYMENT, $status );
			$orderFunc = $orderSearch->createFunction( 'order.containsStatus', $param );

			$expr = array(
				$orderSearch->compare( '>=', 'order.mtime', $limitDate ),
				$orderSearch->compare( '==', 'order.statuspayment', $status ),
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
						$view->extOrderBaseItem = $orderBaseItem;
						$view->extOrderItem = $item;

						$addr = $orderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

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
						$statusItem->setType( MShop_Order_Item_Status_Abstract::EMAIL_PAYMENT );
						$statusItem->setValue( $status );

						$orderStatusManager->saveItem( $statusItem );
					}
					catch( Exception $e )
					{
						$str = 'Error while trying to send payment e-mail for order ID "%1$s" and status "%2$s": %3$s';
						$msg = sprintf( $str, $item->getId(), $item->getPaymentStatus(), $e->getMessage() );
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
