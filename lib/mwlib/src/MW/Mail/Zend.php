<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Mail
 */


/**
 * Zend implementation for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
class MW_Mail_Zend implements MW_Mail_Interface
{
	private $_object;
	private $_transport;


	/**
	 * Initializes the instance of the class.
	 *
	 * @param Zend_Mail $object Zend mail object
	 * @param Zend_Mail_Transport_Abstract|null $transport Mail transport object
	 */
	public function __construct( Zend_Mail $object, Zend_Mail_Transport_Abstract $transport = null )
	{
		$this->_object = $object;
		$this->_transport = $transport;
	}


	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return MW_Mail_Message_Interface E-mail message object
	 */
	public function createMessage( $charset = 'UTF-8' )
	{
		return new MW_Mail_Message_Zend( clone $this->_object );
	}


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param MW_Mail_Message_Interface $message E-mail message object
	 */
	public function send( MW_Mail_Message_Interface $message )
	{
		$message->getObject()->send( $this->_transport );
	}
}
