<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Log
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * MAdmin log item Interface.
 *
 * @package MAdmin
 * @subpackage Log
 */
interface MAdmin_Log_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the facility of the item.
	 *
	 * @return string Returns the facility of the item
	 */
	public function getFacility();

	/**
	 * Sets the new facility of the item.
	 *
	 * @param string $facility Facility of the item
	 */
	public function setFacility( $facility );

	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string Returns the timestamp of the item
	 */
	public function getTimestamp();

	/**
	 * Returns the priority of the item.
	 *
	 * @return integer Returns the priority of the item
	 */
	public function getPriority();

	/**
	 * Sets the new priority of the item.
	 *
	 * @param integer $priority Priority of the item
	 */
	public function setPriority( $priority );

	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message of the item
	 */
	public function getMessage();

	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message of the item
	 */
	public function setMessage( $message );

	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request of the item
	 */
	public function getRequest();

	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request of the item
	 */
	public function setRequest( $request );
}
