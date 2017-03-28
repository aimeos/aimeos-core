<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
abstract class Base
	extends \Aimeos\MShop\Service\Provider\Base
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


	private $beConfig = array(
		'payment.url-success' => array(
			'code' => 'payment.url-success',
			'internalcode'=> 'payment.url-success',
			'label'=> 'Shop URL customers are redirected to after successful payments',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'payment.url-failure' => array(
			'code' => 'payment.url-failure',
			'internalcode'=> 'payment.url-failure',
			'label'=> 'Shop URL customers are redirected to after failed payments',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'payment.url-cancel' => array(
			'code' => 'payment.url-cancel',
			'internalcode'=> 'payment.url-cancel',
			'label'=> 'Shop URL customers are redirected to after canceled payments',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
		'payment.url-update' => array(
			'code' => 'payment.url-update',
			'internalcode'=> 'payment.url-update',
			'label'=> 'Shop URL payment status updates from payment providers are sent to',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$error = $this->checkConfig( $this->beConfig, $attributes );

		foreach( $this->beConfig as $key => $value )
		{
			if( isset( $attributes[$key] ) && $value['type'] != gettype( $attributes[$key] ) ) {
				$error[$key] = sprintf( 'The type of the configuration option with key "%1$s" must be "%2$s"', $key, $value['type'] );
			}
		}

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		$list = [];

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order )
	{
		throw new \Aimeos\MShop\Service\Exception( sprintf( 'Method "%1$s" for provider not available', 'cancel' ) );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order )
	{
		throw new \Aimeos\MShop\Service\Exception( sprintf( 'Method "%1$s" for provider not available', 'capture' ) );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return \Aimeos\MShop\Common\Item\Helper\Form\Standard Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order, array $params = [] )
	{
		$url = $this->getConfigValue( array( 'payment.url-success' ) );

		return new \Aimeos\MShop\Common\Item\Helper\Form\Standard( $url, 'POST', [] );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order )
	{
		throw new \Aimeos\MShop\Service\Exception( sprintf( 'Method "%1$s" for provider not available', 'refund' ) );
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes )
	{
		$this->setAttributes( $orderServiceItem, $attributes, 'payment' );
	}
}