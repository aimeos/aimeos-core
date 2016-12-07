<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @return void
	 */

	public function addListener( \Aimeos\MW\Observer\Listener\Iface $l, $action );


	/**
	 * Removes a listener from a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove listener from
	 * @return void
	 */

	public function removeListener( \Aimeos\MW\Observer\Listener\Iface $l, $action );
}
