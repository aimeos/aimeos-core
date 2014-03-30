<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of delivery emails.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Email_Delivery_Default
	extends Client_Html_Abstract
{
	private $_subPartPath = 'client/html/email/delivery/default/subparts';
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
		$view->deliveryBody = $content;

		$status = $view->extOrderItem->getDeliveryStatus();
		$tplconf = 'client/html/email/delivery/default/template-body';
		$default = array( 'email/delivery/' . $status . '/body-default.html', 'email/delivery/body-default.html' );

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
		$view->deliveryHeader = $content;


		$addr = $view->extOrderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

		$msg = $view->mail();
		$msg->addHeader( 'X-MailGenerator', 'Arcavias' );
		$msg->addTo( $addr->getEMail(), $addr->getFirstName() . ' ' . $addr->getLastName() );


		/** client/html/email/from-name
		 * Name used when sending e-mails
		 *
		 * The name of the person or e-mail account that is used for sending all
		 * shop related emails to customers.
		 *
		 * @param string Name shown in the e-mail
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/delivery/from-name
		 * @see client/html/email/from-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/bcc-email
		 */
		$fromName = $view->config( 'client/html/email/from-name' );

		/** client/html/email/delivery/from-name
		 * Name used when sending delivery e-mails
		 *
		 * The name of the person or e-mail account that is used for sending all
		 * shop related delivery e-mails to customers. This configuration option
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
		$fromNameDelivery = $view->config( 'client/html/email/delivery/from-name', $fromName );

		/** client/html/email/from-email
		 * E-Mail address used when sending e-mails
		 *
		 * The e-mail address of the person or account that is used for sending
		 * all shop related emails to customers.
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/from-name
		 * @see client/html/email/delivery/from-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/bcc-email
		 */
		$fromEmail = $view->config( 'client/html/email/from-email' );

		/** client/html/email/delivery/from-email
		 * E-Mail address used when sending delivery e-mails
		 *
		 * The e-mail address of the person or account that is used for sending
		 * all shop related delivery emails to customers. This configuration option
		 * overwrites the e-mail address set via "client/html/email/from-email".
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/delivery/from-name
		 * @see client/html/email/from-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/bcc-email
		 */
		if( ( $fromEmailDelivery = $view->config( 'client/html/email/delivery/from-email', $fromEmail ) ) != null ) {
			$msg->addFrom( $fromEmailDelivery, $fromNameDelivery );
		}


		/** client/html/email/reply-name
		 * Recipient name displayed when the customer replies to e-mails
		 *
		 * The name of the person or e-mail account the customer should
		 * reply to in case of questions or problems.
		 *
		 * @param string Name shown in the e-mail
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/reply-email
		 * @see client/html/email/delivery/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		$replyName = $view->config( 'client/html/email/reply-name', $fromName );

		/** client/html/email/delivery/reply-name
		 * Recipient name displayed when the customer replies to e-mails
		 *
		 * The name of the person or e-mail account the customer should
		 * reply to in case of questions or problems. This configuration option
		 * overwrites the name set via "client/html/email/reply-name".
		 *
		 * @param string Name shown in the e-mail
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/delivery/reply-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		$replyNameDelivery = $view->config( 'client/html/email/delivery/reply-name', $replyName );

		/** client/html/email/reply-email
		 * E-Mail address used by the customer when replying to e-mails
		 *
		 * The e-mail address of the person or e-mail account the customer
		 * should reply to in case of questions or problems.
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/reply-name
		 * @see client/html/email/delivery/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		$replyEmail = $view->config( 'client/html/email/reply-email', $fromEmail );

		/** client/html/email/delivery/reply-email
		 * E-Mail address used by the customer when replying to e-mails
		 *
		 * The e-mail address of the person or e-mail account the customer
		 * should reply to in case of questions or problems. This configuration
		 * option overwrites the e-mail address set via "client/html/email/reply-email".
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @see client/html/email/delivery/reply-name
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 * @see client/html/email/bcc-email
		 */
		if( ( $replyEmailDelivery = $view->config( 'client/html/email/delivery/reply-email', $replyEmail ) ) != null ) {
			$msg->addReplyTo( $replyEmailDelivery, $replyNameDelivery );
		}


		/** client/html/email/bcc-email
		 * E-Mail address all e-mails should be also sent to
		 *
		 * Using this option you can send a copy of all delivery and payment
		 * related e-mails to a second e-mail account. This can be handy for
		 * testing and checking the e-mails sent to customers.
		 *
		 * It also allows shop owners with a very small volume of orders to be
		 * notified about new orders. Be aware that this isn't useful if the
		 * order volumne is high or has peeks!
		 *
		 * @param string E-mail address
		 * @since 2014.03
		 * @category User
		 * @category Developer
		 * @see client/html/email/delivery/bcc-email
		 * @see client/html/email/reply-email
		 * @see client/html/email/from-email
		 */
		$bccEmail = $view->config( 'client/html/email/bcc-email' );

		/** client/html/email/delivery/bcc-email
		 * E-Mail address all delivery e-mails should be also sent to
		 *
		 * Using this option you can send a copy of all delivery related e-mails
		 * to a second e-mail account. This can be handy for testing and checking
		 * the e-mails sent to customers.
		 *
		 * It also allows shop owners with a very small volume of orders to be
		 * notified about new orders. Be aware that this isn't useful if the
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
		if( ( $bccEmailDelivery = $view->config( 'client/html/email/delivery/bcc-email', $bccEmail ) ) != null ) {
			$msg->addBcc( $bccEmailDelivery );
		}


		$status = $view->extOrderItem->getDeliveryStatus();
		$tplconf = 'client/html/email/delivery/default/template-header';
		$default = array( 'email/delivery/' . $status . '/header-default.html', 'email/delivery/header-default.html' );

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
		return $this->_createSubClient( 'email/delivery/' . $type, $name );
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