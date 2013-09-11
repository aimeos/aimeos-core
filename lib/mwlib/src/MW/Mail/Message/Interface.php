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
interface MW_Mail_Message_Interface
{
	/**
	 * Adds a source e-mail address of the message.
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user sending the e-mail or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addFrom( $email, $name = null );

	/**
	 * Adds a destination e-mail address of the target user mailbox.
	 *
	 * @param string $email Destination address of the target mailbox
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addTo( $email, $name = null );

	/**
	 * Adds a destination e-mail address for a copy of the message.
	 *
	 * @param string $email Destination address for a copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addCc( $email, $name = null );

	/**
	 * Adds a destination e-mail address for a hidden copy of the message.
	 *
	 * @param string $email Destination address for a hidden copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addBcc( $email, $name = null );

	/**
	 * Sets the return e-mail address for the message.
	 *
	 * @param string $email E-mail address which should receive all replies
	 * @param string|null $name Name of the user which should receive all replies or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setReplyTo( $email, $name = null );

	/**
	 * Sets the e-mail address and name of the sender of the message (higher precedence than "From").
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user who sent the message or null for no name
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSender( $email, $name = null );

	/**
	 * Sets the subject of the message.
	 *
	 * @param string $subject Subject of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setSubject( $subject );

	/**
	 * Sets the text body of the message.
	 *
	 * @param string $message Text body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBody( $message );

	/**
	 * Sets the HTML body of the message.
	 *
	 * @param string $message HTML body of the message
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function setBodyHtml( $message );

	/**
	 * Adds an attachment to the message.
	 *
	 * @param string $data Binary or string @author nose
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file (or null if inline disposition is used)
	 * @param string $disposition Type of the disposition ("attachment" or "inline")
	 * @return MW_Mail_Message_Interface Message object
	 */
	public function addAttachment( $data, $mimetype, $filename, $disposition = 'attachment' );
}
