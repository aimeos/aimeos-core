<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Observer
 */


/**
 * Common interface for objects sending notifications.
 *
 * @package MW
 * @subpackage Observer
 */
interface MW_Observer_Publisher_Iface
{
	/**
	 * Adds a listener to a publisher object.
	 *
	 * @param MW_Observer_Listener_Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 * @return void
	 */

	public function addListener( MW_Observer_Listener_Iface $l, $action );


	/**
	 * Removes a listener from a publisher object.
	 *
	 * @param MW_Observer_Listener_Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove listener from
	 * @return void
	 */

	public function removeListener( MW_Observer_Listener_Iface $l, $action );
}
