<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Abstract class for all payment provider implementations.
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
	 * Feature constant if canceling authorizations is supported.
	 */
	const FEAT_CANCEL = 2;

	/**
	 * Feature constant if money authorization and later capture is supported.
	 */
	const FEAT_CAPTURE = 3;

	/**
	 * Feature constant if refunding payments is supported.
	 */
	const FEAT_REFUND = 4;

	/**
	 * Feature constant if reoccurring payments is supported.
	 */
	const FEAT_REPAY = 5;

	/**
	 * Feature constant if payment transfer is supported.
	 */
	const FEAT_TRANSFER = 6;


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return [];
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface|null Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order, array $params = [] ) : ?\Aimeos\MShop\Common\Helper\Form\Iface
	{
		$url = $this->getConfigValue( 'payment.url-success', '' );
		return new \Aimeos\MShop\Common\Helper\Form\Standard( $url, 'POST', [] );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @param \Aimeos\MShop\Price\Item\Iface|null $price Price item with the amount to refund or NULL for whole order
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order, \Aimeos\MShop\Price\Item\Iface $price = null
		) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}


	/**
	 * Executes the payment again for the given order if supported.
	 * This requires support of the payment gateway and token based payment
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function repay( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item with attributes added
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		return $orderServiceItem->addAttributeItems( $this->attributes( $attributes, 'payment' ) );
	}


	/**
	 * Transfers the money to the vendors.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function transfer( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $order;
	}
}
