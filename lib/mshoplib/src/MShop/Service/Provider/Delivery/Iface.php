<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Service
 */

namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Interface with specific methods for delivery providers
 *
 * @package MShop
 * @subpackage Service
 */
interface Iface extends \Aimeos\MShop\Service\Provider\Iface, \Aimeos\MShop\Service\Provider\Factory\Iface
{
	/**
	 * Sends the order details to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
	 * @return null
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order );

	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return null
	 */
	public function processBatch( array $orders );
}