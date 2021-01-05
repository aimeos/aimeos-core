<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue\Message;


/**
 * Common interface for all message implementations
 *
 * @package MW
 * @subpackage MQueue
 */
interface Iface
{
	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function getBody() : string;
}
