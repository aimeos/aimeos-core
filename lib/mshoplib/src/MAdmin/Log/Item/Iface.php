<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MAdmin
 * @subpackage Log
 */


namespace Aimeos\MAdmin\Log\Item;


/**
 * MAdmin log item Interface.
 *
 * @package MAdmin
 * @subpackage Log
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
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
	 * @return void
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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setRequest( $request );
}
