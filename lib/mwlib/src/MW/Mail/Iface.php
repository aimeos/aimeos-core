<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Mail
 */


namespace Aimeos\MW\Mail;


/**
 * Common interface for creating and sending e-mails.
 *
 * @package MW
 * @subpackage Mail
 */
interface Iface
{
	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return \Aimeos\MW\Mail\Message\Iface E-mail message object
	 */
	public function createMessage( string $charset = 'UTF-8' ) : \Aimeos\MW\Mail\Message\Iface;


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param \Aimeos\MW\Mail\Message\Iface $message E-mail message object
	 * @return \Aimeos\MW\Mail\Iface Mail instance for method chaining
	 */
	public function send( \Aimeos\MW\Mail\Message\Iface $message ) : Iface;
}
