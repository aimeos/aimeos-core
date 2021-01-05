<?php


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * Abstract class for all delivery provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base extends \Aimeos\MShop\Service\Provider\Base implements Iface
{
	/**
	 * Feature constant if querying for status updates for an order is supported.
	 */
	const FEAT_QUERY = 1;


	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\Map Updated order items
	 */
	public function processBatch( iterable $orders ) : \Aimeos\Map
	{
		foreach( $orders as $key => $order ) {
			$orders[$key] = $this->getObject()->process( $order );
		}

		return map( $orders );
	}


	/**
	 * Sets the delivery attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order service item with attributes added
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		return $this->setAttributes( $orderServiceItem, $attributes, 'delivery' );
	}
}
