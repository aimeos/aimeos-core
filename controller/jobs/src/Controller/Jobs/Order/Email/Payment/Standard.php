<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Order
 */


namespace Aimeos\Controller\Jobs\Order\Email\Payment;


/**
 * Order payment e-mail job controller.
 *
 * @package Controller
 * @subpackage Order
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Order payment related e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Sends order confirmation or payment status update e-mails' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$aimeos = $this->getAimeos();
		$context = $this->getContext();
		$config = $context->getConfig();
		$mailer = $context->getMail();
		$view = $context->getView();

		$templatePaths = $aimeos->getCustomPaths( 'client/html' );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$client = \Aimeos\Client\Html\Email\Payment\Factory::createClient( $context, $templatePaths );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		/** controller/jobs/order/email/payment/standard/limit-days
		 * Only send payment e-mails of orders that were created in the past within the configured number of days
		 *
		 * The payment e-mails are normally send immediately after the payment
		 * status has changed. This option prevents e-mails for old order from
		 * being send in case anything went wrong or an update failed to avoid
		 * confusion of customers.
		 *
		 * @param integer Number of days
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/delivery/standard/limit-days
		 * @see controller/jobs/service/delivery/process/limit-days
		 */
		$limit = $config->get( 'controller/jobs/order/email/payment/standard/limit-days', 30 );
		$limitDate = date( 'Y-m-d H:i:s', time() - $limit * 86400 );

		$default = array(
			\Aimeos\MShop\Order\Item\Base::PAY_REFUND,
			\Aimeos\MShop\Order\Item\Base::PAY_PENDING,
			\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED,
			\Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
		);

		/** controller/jobs/order/email/payment/standard/status
		 * Only send order payment notification e-mails for these payment status values
		 *
		 * Notification e-mail about payment status changes can be sent for these
		 * status values:
		 * * 0: deleted
		 * * 1: canceled
		 * * 2: refused
		 * * 3: refund
		 * * 4: pending
		 * * 5: authorized
		 * * 6: received
		 *
		 * User-defined status values are possible but should be in the private
		 * block of values between 30000 and 32767.
		 *
		 * @param integer Payment status constant
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see controller/jobs/order/email/delivery/standard/status
		 * @see controller/jobs/order/email/payment/standard/limit-days
		 */
		foreach( (array) $config->get( 'controller/jobs/order/email/payment/standard/status', $default ) as $status )
		{
			$orderSearch = $orderManager->createSearch();

			$param = array( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_PAYMENT, $status );
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

						$addr = $orderBaseItem->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

						$view->extAddressItem = $addr;
						$view->extOrderBaseItem = $orderBaseItem;
						$view->extOrderItem = $item;

						$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $addr->getLanguageId() ) );
						$view->addHelper( 'translate', $helper );

						$message = $mailer->createMessage();
						$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
						$view->addHelper( 'mail', $helper );

						$client->setView( $view );
						$client->getHeader();
						$client->getBody();

						$mailer->send( $message );

						$statusItem = $orderStatusManager->createItem();
						$statusItem->setParentId( $id );
						$statusItem->setType( \Aimeos\MShop\Order\Item\Status\Base::EMAIL_PAYMENT );
						$statusItem->setValue( $status );

						$orderStatusManager->saveItem( $statusItem );
					}
					catch( \Exception $e )
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
