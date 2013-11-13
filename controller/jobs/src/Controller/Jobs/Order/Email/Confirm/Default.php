<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Jobs admin job controller for admin interfaces.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Order_Email_Confirm_Default
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
		return $this->_getContext()->getI18n()
			->dt( 'controller/jobs', 'Order confirmation e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()
			->dt( 'controller/jobs', 'Sends a confirmation e-mail to the customer for each completed order' );
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


		$config->set( 'client/html/email/confirm/main/html/encoded', false );
		$config->set( 'client/html/email/confirm/main/text/encoded', false );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );


		$client = Client_Html_Email_Confirm_Factory::createClient( $context, $templatePaths );
		$mainClient = $client->getSubClient( 'main' );
		$htmlClient = $mainClient->getSubClient( 'html' );
		$textClient = $mainClient->getSubClient( 'text' );


		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderStatusManager = $orderManager->getSubManager( 'status' );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$orderSearch = $orderManager->createSearch();

		$param = array( MShop_Order_Item_Status_Abstract::EMAIL_ACCEPTED, 1 );
		$orderFunc = $orderSearch->createFunction( 'order.containsStatus', $param );

		$expr = array(
			$orderSearch->compare( '>', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_PENDING ),
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
					$view->confirmOrderBaseItem = $orderBaseItem;
					$view->confirmOrderItem = $item;

					$addr = $orderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

					$helper = new MW_View_Helper_Translate_Default( $view, $context->getI18n( $addr->getLanguageId() ) );
					$view->addHelper( 'translate', $helper );

					$htmlClient->setView( $view );
					$textClient->setView( $view );

					$name = sprintf( $view->translate( 'client/html', '%1$s %2$s' ), $addr->getFirstname(), $addr->getLastname() );
					$subject = sprintf( $view->translate( 'client/html', 'Confirmation for order %1$s' ), $item->getId() );

					$senderEmail = $config->get( 'controller/jobs/order/email/confirm/sender-email', 'noreply@example.com' );
					$senderName = $config->get( 'controller/jobs/order/email/confirm/sender-name', '' );

					$message = $mailer->createMessage();
					$message->setSender( $senderEmail, $senderName );
					$message->addTo( $addr->getEmail(), $name );
					$message->setSubject( $subject );
					$message->setBody( $textClient->getBody() );
					$message->setBodyHtml( $htmlClient->getBody() );
					$mailer->send( $message );

					$statusItem = $orderStatusManager->createItem();
					$statusItem->setParentId( $id );
					$statusItem->setType( MShop_Order_Item_Status_Abstract::EMAIL_ACCEPTED );
					$statusItem->setValue( 1 );

					$orderStatusManager->saveItem( $statusItem );
				}
				catch( Exception $e )
				{
					$msg = sprintf( 'Error while trying to send confirmation e-mail for order ID "%1$s": %2$s', $item->getId(), $e->getMessage() );
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
