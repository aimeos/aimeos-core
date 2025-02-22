<?php


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
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
	 * Sets the delivery attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $orderServiceItem->addAttributeItems( $this->attributes( $attributes, 'delivery' ) );
	}
}
