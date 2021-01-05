<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Observer
 */


namespace Aimeos\MW\Observer\Publisher;


/**
 * Common interface for objects sending notifications.
 *
 * @package MW
 * @subpackage Observer
 */
interface Iface
{
	/**
	 * Adds a listener to a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
	 */

	public function attach( \Aimeos\MW\Observer\Listener\Iface $l, string $action ) : Iface;


	/**
	 * Removes a listener from a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove listener from
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
	 */

	public function detach( \Aimeos\MW\Observer\Listener\Iface $l, string $action ) : Iface;


	/**
	 * Removes all attached listeners from the publisher
	 *
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
	 */
	public function off() : Iface;
}
