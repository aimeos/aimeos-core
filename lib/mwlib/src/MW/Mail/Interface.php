<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Mail
 */


/**
 * Common interface for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
interface MW_Mail_Interface
{
	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return MW_Mail_Message_Interface E-mail message object
	 */
	public function createMessage( $charset = 'UTF-8' );


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param MW_Mail_Message_Interface $message E-mail message object
	 * @return void
	 */
	public function send( MW_Mail_Message_Interface $message );
}
