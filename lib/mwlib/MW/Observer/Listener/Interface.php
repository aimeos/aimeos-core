<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Observer
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Common interface for objects listening to notifications.
 *
 * @package MW
 * @subpackage Observer
 */
interface MW_Observer_Listener_Interface
{
	/**
	 * Subscribes itself to a publisher.
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p );

	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return boolean Status of the operation (true=OK, false=not OK)
	 */
	public function update( MW_Observer_Publisher_Interface $p, $action, $value = null );
}
