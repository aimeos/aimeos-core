<?php


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
abstract class Base extends \Aimeos\MShop\Service\Provider\Base
{
	/**
	 * Feature constant if querying for status updates for an order is supported.
	 */
	const FEAT_QUERY = 1;

	const ERR_OK = 0;
	const ERR_TEMP = 1;
	const ERR_XML = 10;
	const ERR_SCHEMA = 11;


	/**
	 * Sets the delivery attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes )
	{
		$this->setAttributes( $orderServiceItem, $attributes, 'delivery' );
	}
}