<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Observer
 */


namespace Aimeos\MW\Observer\Listener;


/**
 * Common interface for objects listening to notifications.
 *
 * @package MW
 * @subpackage Observer
 */
interface Iface
{
	/**
	 * Subscribes itself to a publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return void
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p );

	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return boolean Status of the operation (true=OK, false=not OK)
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $p, $action, $value = null );
}
