<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Mail
 */


namespace Aimeos\MW\Mail\Message;


/**
 * Common interface for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
interface Iface
{
	/**
	 * Adds a source e-mail address of the message.
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user sending the e-mail or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addFrom( string $email, string $name = null ) : Iface;

	/**
	 * Adds a destination e-mail address of the target user mailbox.
	 *
	 * @param string $email Destination address of the target mailbox
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addTo( string $email, string $name = null ) : Iface;

	/**
	 * Adds a destination e-mail address for a copy of the message.
	 *
	 * @param string $email Destination address for a copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addCc( string $email, string $name = null ) : Iface;

	/**
	 * Adds a destination e-mail address for a hidden copy of the message.
	 *
	 * @param string $email Destination address for a hidden copy
	 * @param string|null $name Name of the user owning the target mailbox or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addBcc( string $email, string $name = null ) : Iface;

	/**
	 * Adds the return e-mail address for the message.
	 *
	 * @param string $email E-mail address which should receive all replies
	 * @param string|null $name Name of the user which should receive all replies or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addReplyTo( string $email, string $name = null ) : Iface;

	/**
	 * Adds a custom header to the message.
	 *
	 * @param string $name Name of the custom e-mail header
	 * @param string $value Text content of the custom e-mail header
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addHeader( string $name, string $value ) : Iface;

	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function send() : Iface;

	/**
	 * Sets the e-mail address and name of the sender of the message (higher precedence than "From").
	 *
	 * @param string $email Source e-mail address
	 * @param string|null $name Name of the user who sent the message or null for no name
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function setSender( string $email, string $name = null ) : Iface;

	/**
	 * Sets the subject of the message.
	 *
	 * @param string $subject Subject of the message
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function setSubject( string $subject ) : Iface;

	/**
	 * Sets the text body of the message.
	 *
	 * @param string $message Text body of the message
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function setBody( string $message ) : Iface;

	/**
	 * Sets the HTML body of the message.
	 *
	 * @param string $message HTML body of the message
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function setBodyHtml( string $message ) : Iface;

	/**
	 * Adds an attachment to the message.
	 *
	 * @param string $data Binary or string @author nose
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file (or null if inline disposition is used)
	 * @param string $disposition Type of the disposition ("attachment" or "inline")
	 * @return \Aimeos\MW\Mail\Message\Iface Message object
	 */
	public function addAttachment( string $data, string $mimetype, string $filename, string $disposition = 'attachment' ) : Iface;

	/**
	 * Embeds an attachment into the message and returns its reference.
	 *
	 * @param string $data Binary or string
	 * @param string $mimetype Mime type of the attachment (e.g. "text/plain", "application/octet-stream", etc.)
	 * @param string|null $filename Name of the attached file
	 * @return string Content ID for referencing the attachment in the HTML body
	 */
	public function embedAttachment( string $data, string $mimetype, string $filename ) : string;
}
