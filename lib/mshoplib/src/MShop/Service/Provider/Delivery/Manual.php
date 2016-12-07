<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Manual delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class Manual
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{
	/**
	 * Updates the delivery status.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order instance
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}

}
