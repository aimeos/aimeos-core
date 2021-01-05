<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getFacility() : string;

	/**
	 * Sets the new facility of the item.
	 *
	 * @param string $facility Facility of the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setFacility( string $facility ) : \Aimeos\MAdmin\Log\Item\Iface;

	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string|null Returns the timestamp of the item
	 */
	public function getTimestamp() : ?string;

	/**
	 * Returns the priority of the item.
	 *
	 * @return int Returns the priority of the item
	 */
	public function getPriority() : int;

	/**
	 * Sets the new priority of the item.
	 *
	 * @param int $priority Priority of the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setPriority( int $priority ) : \Aimeos\MAdmin\Log\Item\Iface;

	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message of the item
	 */
	public function getMessage() : string;

	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message of the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setMessage( string $message ) : \Aimeos\MAdmin\Log\Item\Iface;

	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request of the item
	 */
	public function getRequest() : string;

	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request of the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setRequest( string $request ) : \Aimeos\MAdmin\Log\Item\Iface;
}
