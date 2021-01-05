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
 * Black hole e-mail implementation.
 *
 * @package MW
 * @subpackage Mail
 */
class None implements \Aimeos\MW\Mail\Iface
{
	/**
	 * Creates a new e-mail message object.
	 *
	 * @param string $charset Default charset of the message
	 * @return \Aimeos\MW\Mail\Message\Iface E-mail message object
	 */
	public function createMessage( string $charset = 'UTF-8' ) : \Aimeos\MW\Mail\Message\Iface
	{
		return new \Aimeos\MW\Mail\Message\None();
	}


	/**
	 * Sends the e-mail message to the mail server.
	 *
	 * @param \Aimeos\MW\Mail\Message\Iface $message E-mail message object
	 * @return \Aimeos\MW\Mail\Iface Mail instance for method chaining
	 */
	public function send( \Aimeos\MW\Mail\Message\Iface $message ) : Iface
	{
		return $this;
	}
}
