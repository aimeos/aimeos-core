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
	implements Client_Html_Interface
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
		$fromEmail = $view->config( 'client/html/email/from-email' );
		$fromName = $view->config( 'client/html/email/from-name' );

		$msg = $view->mail();
		$msg->addHeader( 'X-MailGenerator', 'Arcavias' );
		$msg->addTo( $addr->getEMail(), $addr->getFirstName() . ' ' . $addr->getLastName() );
		$msg->addFrom( $fromEmail, $fromName );
		$msg->addReplyTo(
			$view->config( 'client/html/email/reply-email', $fromEmail ),
			$view->config( 'client/html/email/reply-name', $fromName )
		);

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