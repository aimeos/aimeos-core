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
interface MW_Observer_Publisher_Interface
{
	/**
	 * Adds a listener to a publisher object.
	 *
	 * @param MW_Observer_Listener_Interface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 */

	public function addListener( MW_Observer_Listener_Interface $l, $action );


	/**
	 * Removes a listener from a publisher object.
	 *
	 * @param MW_Observer_Listener_Interface $l Object implementing listener interface
	 * @param string $action Name of the action to remove listener from
	 */

	public function removeListener( MW_Observer_Listener_Interface $l, $action );
}
