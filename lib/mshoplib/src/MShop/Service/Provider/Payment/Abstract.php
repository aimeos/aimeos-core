<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for all payment provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Payment_Abstract
	extends MShop_Service_Provider_Abstract
	implements MShop_Service_Provider_Payment_Interface
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


	private $_beConfig = array(
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
		$error = $this->_checkConfig( $this->_beConfig, $attributes );

		foreach ( $this->_beConfig as $key => $value )
		{
			if( isset( $attributes[$key] ) && $value['type'] != gettype( $attributes[$key] ) ) {
				$error[ $key ] = sprintf( 'The type of the configuration option with key "%1$s" must be "%2$s"', $key, $value['type'] );
			}
		}

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = array();

		foreach( $this->_beConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'cancel' ) );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function capture( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'capture' ) );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return MShop_Common_Item_Helper_Form_Default Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order, array $params = array() )
	{
		$url = $this->_getConfigValue( array( 'payment.url-success' ) );

		return new MShop_Common_Item_Helper_Form_Default( $url, 'POST', array() );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function refund( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'refund' ) );
	}


	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
	{
		$this->_setAttributes( $orderServiceItem, $attributes, 'payment' );
	}
}