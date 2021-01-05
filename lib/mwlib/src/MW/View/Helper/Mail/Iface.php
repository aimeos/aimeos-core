<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Mail;


/**
 * View helper class for creating e-mails.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the e-mail message object.
	 *
	 * @return \Aimeos\MW\Mail\Message\Iface E-mail message object
	 */
	public function transform() : \Aimeos\MW\Mail\Message\Iface;
}
