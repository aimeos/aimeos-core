<?php 

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */

namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Interface with specific methods for delivery providers.
 *
 * @package MShop
 * @subpackage Service
 */
interface Iface extends \Aimeos\MShop\Service\Provider\Factory\Iface
{
	/**
	 * Sends the order details to the ERP system for further processing.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object to process
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order );
}