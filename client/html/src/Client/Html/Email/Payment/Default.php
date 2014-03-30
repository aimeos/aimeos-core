<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of payment emails.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Email_Payment_Default
	extends Client_Html_Abstract
{
	private $_subPartPath = 'client/html/email/payment/default/subparts';
	private $_subPartNames = array( 'text', 'html' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$content = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$content .= $subclient->setView( $view )->getBody();
		}
		$view->paymentBody = $content;

		$status = $view->extOrderItem->getPaymentStatus();
		$tplconf = 'client/html/email/payment/default/template-body';
		$default = array( 'email/payment/' . $status . '/body-default.html', 'email/payment/body-default.html' );

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_setViewParams( $this->getView() );

		$content = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$content .= $subclient->setView( $view )->getHeader();
		}
		$view->paymentHeader = $content;


		$addr = $view->extOrderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

		$msg = $view->mail();
		$msg->addHeader( 'X-MailGenerator', 'Arcavias' );
		$msg->addTo( $addr->getEMail(), $addr->getFirstName() . ' ' . $addr->getLastName() );


		/** client/html/email/from-name
		 * @see client/html/email/payment/from-name
		 */
		$fromName = $view->config( 'client/html/email/from-name' );

		/** client/html/email/payment/from-name
		 * Name used when sending payment e-mails
		 *
		 * The name of the person or e-mail account that is used for sending all
		 * shop related payment e-mails to customers. This configuration option
		 * overwrite the name set in "client/html/email/from-name".
		 *
		 * @param string Name shown in the e-mail
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/from-name
		 * @see client/html/email/from-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/bcc-email
		 */
		$fromNamePayment = $view->config( 'client/html/email/payment/from-name', $fromName );

		/** client/html/email/from-email
		 * @see client/html/email/payment/from-email
		 */
		$fromEmail = $view->config( 'client/html/email/from-email' );

		/** client/html/email/payment/from-email
		 * E-Mail address used when sending payment e-mails
		 *
		 * The e-mail address of the person or account that is used for sending
		 * all shop related payment emails to customers. This configuration option
		 * overwrites the e-mail address set via "client/html/email/from-email".
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/payment/from-name
		 * @see client/html/email/from-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/bcc-email
		 */
		if( ( $fromEmailPayment = $view->config( 'client/html/email/payment/from-email', $fromEmail ) ) != null ) {
			$msg->addFrom( $fromEmailPayment, $fromNamePayment );
		}


		/** client/html/email/reply-name
		 * @see client/html/email/payment/reply-email
		 */
		$replyName = $view->config( 'client/html/email/reply-name', $fromName );

		/** client/html/email/payment/reply-name
		 * Recipient name displayed when the customer replies to payment e-mails
		 *
		 * The name of the person or e-mail account the customer should
		 * reply to in case of payment related questions or problems. This
		 * configuration option overwrites the name set via
		 * "client/html/email/reply-name".
		 *
		 * @param string Name shown in the e-mail
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/payment/reply-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		$replyNamePayment = $view->config( 'client/html/email/payment/reply-name', $replyName );

		/** client/html/email/reply-email
		 * @see client/html/email/payment/reply-email
		 */
		$replyEmail = $view->config( 'client/html/email/reply-email', $fromEmail );

		/** client/html/email/payment/reply-email
		 * E-Mail address used by the customer when replying to payment e-mails
		 *
		 * The e-mail address of the person or e-mail account the customer
		 * should reply to in case of payment related questions or problems.
		 * This configuration option overwrites the e-mail address set via
		 * "client/html/email/reply-email".
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/payment/reply-name
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		if( ( $replyEmailPayment = $view->config( 'client/html/email/payment/reply-email', $replyEmail ) ) != null ) {
			$msg->addReplyTo( $replyEmailPayment, $replyNamePayment );
		}


		/** client/html/email/bcc-email
		 * @see client/html/email/payment/bcc-email
		 */
		$bccEmail = $view->config( 'client/html/email/bcc-email' );

		/** client/html/email/payment/bcc-email
		 * E-Mail address all payment e-mails should be also sent to
		 *
		 * Using this option you can send a copy of all payment related e-mails
		 * to a second e-mail account. This can be handy for testing and checking
		 * the e-mails sent to customers.
		 *
		 * It also allows shop owners with a very small volume of orders to be
		 * notified about payment changes. Be aware that this isn't useful if the
		 * order volumne is high or has peeks!
		 *
		 * This configuration option overwrites the e-mail address set via
		 * "client/html/email/bcc-email".
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see client/html/email/bcc-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 */
		if( ( $bccEmailPayment = $view->config( 'client/html/email/payment/bcc-email', $bccEmail ) ) != null ) {
			$msg->addBcc( $bccEmailPayment );
		}


		$status = $view->extOrderItem->getPaymentStatus();
		$tplconf = 'client/html/email/payment/default/template-header';
		$default = array( 'email/payment/' . $status . '/header-default.html', 'email/payment/header-default.html' );

		return $view->render( $this->_getTemplate( $tplconf, $default ) );;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'email/payment/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_process( $this->_subPartPath, $this->_subPartNames );
	}
}