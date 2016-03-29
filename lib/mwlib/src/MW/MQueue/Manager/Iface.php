<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MW
 * @subpackage MQueue
 */


namespace Aimeos\MW\MQueue\Manager;


/**
 * Interface for message queue managers
 *
 * @package MW
 * @subpackage MQueue
 */
interface Iface
{
	/**
	 * Returns the message queue for the given name
	 *
	 * @param string $resource Resource name of the message queue
	 * @return \Aimeos\MW\MQueue\Iface Message queue object
	 * @throws \Aimeos\MW\MQueue\Exception If an error occurs
	 */
	public function get( $resource );
}