<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Payment provider for post-paid orders.
 *
 * @package MShop
 * @subpackage Service
 */
class PostPay
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{
	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @param array &$header Response headers for notification requests
	 * @return \Aimeos\MShop\Order\Item\Iface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateSync( array $params = [], $body = null, &$response = null, array &$header = [] )
	{
		if( isset( $params['orderid'] ) )
		{
			$order = $this->getOrder( $params['orderid'] );
			$order->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
			$this->saveOrder( $order );

			return $order;
		}
	}
}