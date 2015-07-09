<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Payment provider for paypal express orders.
 *
 * @package MShop
 * @subpackage Service
 */

class MShop_Service_Provider_Payment_PayPalExpress
	extends MShop_Service_Provider_Payment_Abstract
	implements MShop_Service_Provider_Payment_Interface
{
	private $_apiendpoint;

	private $_beConfig = array(
		'paypalexpress.ApiUsername' => array(
			'code' => 'paypalexpress.ApiUsername',
			'internalcode'=> 'paypalexpress.ApiUsername',
			'label'=> 'Username',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'paypalexpress.AccountEmail' => array(
			'code' => 'paypalexpress.AccountEmail',
			'internalcode'=> 'paypalexpress.AccountEmail',
			'label'=> 'Registered e-mail address of the shop owner in PayPal',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'paypalexpress.ApiPassword' => array(
			'code' => 'paypalexpress.ApiPassword',
			'internalcode'=> 'paypalexpress.ApiPassword',
			'label'=> 'Password',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'paypalexpress.ApiSignature' => array(
			'code' => 'paypalexpress.ApiSignature',
			'internalcode'=> 'paypalexpress.ApiSignature',
			'label'=> 'Signature',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'paypalexpress.ApiEndpoint' => array(
			'code' => 'paypalexpress.ApiEndpoint',
			'internalcode'=> 'paypalexpress.ApiEndpoint',
			'label'=> 'APIEndpoint',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'https://api-3t.paypal.com/nvp',
			'required'=> false,
		),
		'paypalexpress.PaypalUrl' => array(
			'code' => 'paypalexpress.PaypalUrl',
			'internalcode'=> 'paypalexpress.PaypalUrl',
			'label'=> 'PaypalUrl',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
			'required'=> false,
		),
		'paypalexpress.PaymentAction' => array(
			'code' => 'paypalexpress.PaymentAction',
			'internalcode'=> 'paypalexpress.PaymentAction',
			'label'=> 'PaymentAction',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'Sale',
			'required'=> false,
		),
		'paypalexpress.url-validate' => array(
			'code' => 'paypalexpress.url-validate',
			'internalcode'=> 'paypalexpress.url-validate',
			'label'=> 'Validation URL',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'https://www.paypal.com/webscr&cmd=_notify-validate',
			'required'=> false,
		),
	);


	/**
	 * Initializes the provider object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration
	 * @throws MShop_Service_Exception If one of the required configuration values isn't available
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem )
	{
		parent::__construct( $context, $serviceItem );

		$configParameters = array(
			'paypalexpress.AccountEmail',
			'paypalexpress.ApiUsername',
			'paypalexpress.ApiPassword',
			'paypalexpress.ApiSignature',
		);

		$config = $serviceItem->getConfig();

		foreach( $configParameters as $param )
		{
			if( !isset( $config[ $param ] ) ) {
				throw new MShop_Service_Exception( sprintf( 'Parameter "%1$s" for configuration not available', $param ) );
			}
		}

		$default = 'https://api-3t.paypal.com/nvp';
		$this->_apiendpoint = $this->_getConfigValue( array( 'paypalexpress.ApiEndpoint' ), $default );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = parent::getConfigBE();

		foreach( $this->_beConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->_checkConfig( $this->_beConfig, $attributes ) );
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
		$orderid = $order->getId();
		$orderBaseItem = $this->_getOrderBase( $order->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ALL );

		$values = $this->_getOrderDetails( $orderBaseItem );
		$values[ 'METHOD' ] = 'SetExpressCheckout';
		$values[ 'PAYMENTREQUEST_0_INVNUM' ] = $orderid;
		$values[ 'RETURNURL' ] = $this->_getConfigValue( array( 'payment.url-success' ) );
		$values[ 'CANCELURL' ] = $this->_getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $orderid, $response, __METHOD__ );

		$default = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s';
		$paypalUrl = sprintf( $this->_getConfigValue( array( 'paypalexpress.PaypalUrl' ), $default ), $rvals['TOKEN'] );

		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT;
		$this->_setAttributes( $orderBaseItem->getService( $type ), array ( 'TOKEN' => $rvals['TOKEN'] ), 'payment/paypal' );
		$this->_saveOrderBase( $orderBaseItem );

		return new MShop_Common_Item_Helper_Form_Default( $paypalUrl, 'POST', array() );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order )
	{
		if( ( $tid = $this->_getOrderServiceItem( $order->getBaseId() )->getAttribute('TRANSACTIONID', 'payment/paypal') ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'GetTransactionDetails';
		$values['TRANSACTIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$this->_setPaymentStatus( $order, $rvals );
		$this->_saveOrder( $order );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function capture( MShop_Order_Item_Interface $order )
	{
		$baseid = $order->getBaseId();
		$baseItem = $this->_getOrderBase( $baseid );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		if( ( $tid = $serviceItem->getAttribute('TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoCapture';
		$values['COMPLETETYPE'] = 'Complete';
		$values['AUTHORIZATIONID'] = $tid;
		$values['INVNUM'] = $order->getId();
		$values['CURRENCYCODE'] = $baseItem->getPrice()->getCurrencyId();
		$values['AMT'] = $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getCosts();

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$this->_setPaymentStatus( $order, $rvals );

		$attributes = array();
		if( isset( $rvals['PARENTTRANSACTIONID'] ) ) {
			$attributes['PARENTTRANSACTIONID'] = $rvals['PARENTTRANSACTIONID'];
		}

		//updates the transaction id
		$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
		$this->_setAttributes( $serviceItem, $attributes, 'payment/paypal' );

		$this->_saveOrderBase( $baseItem );
		$this->_saveOrder( $order );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function refund( MShop_Order_Item_Interface $order )
	{
		$baseItem = $this->_getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		if( ( $tid = $serviceItem->getAttribute('TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'RefundTransaction';
		$values['REFUNDSOURCE'] = 'instant';
		$values['REFUNDTYPE'] = 'Full';
		$values['TRANSACTIONID'] = $tid;
		$values['INVOICEID'] = $order->getId();

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'REFUNDTRANSACTIONID' => $rvals['REFUNDTRANSACTIONID'] );
		$this->_setAttributes( $serviceItem, $attributes, 'payment/paypal' );
		$this->_saveOrderBase( $baseItem );

		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUND );
		$this->_saveOrder( $order );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		if( ( $tid = $this->_getOrderServiceItem( $order->getBaseId() )->getAttribute('TRANSACTIONID', 'payment/paypal') ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoVoid';
		$values['AUTHORIZATIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_CANCELED );
		$this->_saveOrder( $order );
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @param array &$header Response headers for notification requests
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( array $params = array(), $body = null, &$response = null, array &$header = array() )
	{
		if( isset( $params['token'] ) && isset( $params['PayerID'] ) && isset( $params['orderid'] ) ) {
			return $this->_doExpressCheckoutPayment( $params );
		}

		//tid from ipn
		if( !isset( $params['txn_id'] ) ) {
			return null;
		}

		$urlQuery = http_build_query( $params, '', '&' );

		//validation
		$response = $this->_getCommunication()->transmit( $this->_getConfigValue( array( 'paypalexpress.url-validate' ) ), 'POST', $urlQuery );


		if( $response !== 'VERIFIED' )
		{
			$msg = sprintf( 'PayPal Express: Invalid request "%1$s"', $urlQuery );
			throw new MShop_Service_Exception( $msg );
		}


		$order = $this->_getOrder( $params['invoice'] );
		$baseItem = $this->_getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$this->_checkIPN( $baseItem, $params );

		$status = array( 'PAYMENTSTATUS' => $params['payment_status'] );

		if( isset( $params['pending_reason'] ) ) {
			$status['PENDINGREASON'] = $params['pending_reason'];
		}

		$this->_setAttributes( $serviceItem, array( $params['txn_id'] => $params['payment_status'] ), 'payment/paypal/txn' );
		$this->_setAttributes( $serviceItem, array( 'TRANSACTIONID' => $params['txn_id'] ), 'payment/paypal' );
		$this->_saveOrderBase( $baseItem );

		$this->_setPaymentStatus( $order, $status );
		$this->_saveOrder( $order );

		return $order;
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		switch( $what )
		{
			case MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE:
			case MShop_Service_Provider_Payment_Abstract::FEAT_QUERY:
			case MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL:
			case MShop_Service_Provider_Payment_Abstract::FEAT_REFUND:
				return true;
		}

		return false;
	}

	/**
	 * Begins paypalexpress transaction and saves transaction id.
	 *
	 * @param mixed $params Update information whose format depends on the payment provider
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 */
	protected function _doExpressCheckoutPayment( $params )
	{
		$order = $this->_getOrder( $params['orderid'] );
		$baseid = $order->getBaseId();
		$baseItem = $this->_getOrderBase( $baseid );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoExpressCheckoutPayment';
		$values['TOKEN'] = $params['token'];
		$values['PAYERID'] = $params['PayerID'];
		$values['PAYMENTACTION'] = $this->_getConfigValue( array( 'paypalexpress.PaymentAction' ), 'Sale' );
		$values['CURRENCYCODE'] = $baseItem->getPrice()->getCurrencyId();
		$values['NOTIFYURL'] = $this->_getConfigValue( array( 'payment.url-update', 'payment.url-success' ) );
		$values['AMT'] = ( $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getCosts() );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'PAYERID' => $params['PayerID'] );

		if( isset( $rvals['TRANSACTIONID'] ) )
		{
			$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
			$this->_setAttributes( $serviceItem, array( $rvals['TRANSACTIONID'] => $rvals['PAYMENTSTATUS'] ), 'payment/paypal/txn' );
		}

		$this->_setAttributes( $serviceItem, $attributes, 'payment/paypal' );
		$this->_saveOrderBase( $baseItem );

		$this->_setPaymentStatus( $order, $rvals );
		$this->_saveOrder( $order );

		return $order;
	}


	/**
	 * Checks the response from the payment server.
	 *
	 * @param string $orderid Order item ID
	 * @param string $response Response from the payment provider
	 * @param string $method Name of the calling method
	 * @return array Associative list of key/value pairs containing the response parameters
	 * @throws MShop_Service_Exception If request was not successful and an error was returned
	 */
	protected function _checkResponse( $orderid, $response, $method )
	{
		$rvals = array();
		parse_str( $response, $rvals );

		if( $rvals['ACK'] !== 'Success' )
		{
			$msg = 'PayPal Express: method = ' . $method . ', order ID = ' . $orderid . ', response = ' . print_r( $rvals, true );
			$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::INFO );

			if( $rvals['ACK'] !== 'SuccessWithWarning' )
			{
				$short = ( isset( $rvals['L_SHORTMESSAGE0'] ) ? $rvals['L_SHORTMESSAGE0'] : '<none>' );
				$msg = sprintf( 'PayPal Express: Request for order ID "%1$s" failed with "%2$s"', $orderid, $short );
				throw new MShop_Service_Exception( $msg );
			}
		}

		return $rvals;
	}


	/**
	 * Checks if IPN message from paypal is valid.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket
	 * @param array $params
	 * @todo 2016.03 Remove $baseManager parameter
	 */
	protected function _checkIPN( $basket, $params )
	{
		$attrManager = MShop_Factory::createManager( $this->_getContext(), 'order/base/service/attribute' );

		if( $this->_getConfigValue( array( 'paypalexpress.AccountEmail' ) ) !== $params['receiver_email'] )
		{
			$msg = sprintf( 'PayPal Express: Wrong receiver email "%1$s"', $params['receiver_email'] );
			throw new MShop_Service_Exception( $msg );
		}

		$price = $basket->getPrice();
		$amount = $price->getValue() + $price->getCosts();
		if( $amount != $params['payment_amount'] )
		{
			$msg = sprintf( 'PayPal Express: Wrong payment amount "%1$s" for order ID "%2$s"', $params['payment_amount'], $params['invoice'] );
			throw new MShop_Service_Exception( $msg );
		}

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.service.attribute.code', $params['txn_id'] ),
			$search->compare( '==', 'order.base.service.attribute.value', $params['payment_status'] ),
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $attrManager->searchItems( $search );

		if ( ( $attr = reset( $results ) ) !== false )
		{
			$msg = sprintf( 'PayPal Express: Duplicate transaction with ID "%1$s" and status "%2$s" ', $params['txn_id'], $params['txn_status'] );
			throw new MShop_Service_Exception( $msg );
		}
	}


	/**
	 * Maps the PayPal status to the appropriate payment status and sets it in the order object.
	 *
	 * @param MShop_Order_Item_Interface $invoice Order invoice object
	 * @param array $response Associative list of key/value pairs containing the PayPal response
	 */
	protected function _setPaymentStatus( MShop_Order_Item_Interface $invoice, array $response )
	{
		if( !isset( $response['PAYMENTSTATUS'] ) ) {
			return;
		}

		switch ( $response['PAYMENTSTATUS'] )
		{
			case 'Pending':
				if( isset( $response['PENDINGREASON'] ) )
				{
					if( $response['PENDINGREASON'] === 'authorization' )
					{
						$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_AUTHORIZED );
						break;
					}

					$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', PENDINGREASON = ' . $response['PENDINGREASON'];
					$this->_getContext()->getLogger()->log( $str, MW_Logger_Abstract::INFO );
				}

				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_PENDING );
				break;

			case 'In-Progress':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_PENDING );
				break;

			case 'Completed':
			case 'Processed':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_RECEIVED );
				break;

			case 'Failed':
			case 'Denied':
			case 'Expired':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUSED );
				break;

			case 'Refunded':
			case 'Partially-Refunded':
			case 'Reversed':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUND );
				break;

			case 'Canceled-Reversal':
			case 'Voided':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_CANCELED );
				break;

			default:
				$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', response = ' . print_r( $response, true );
				$this->_getContext()->getLogger()->log( $str, MW_Logger_Abstract::INFO );
		}
	}


	/**
	 * Returns an list of order data required by PayPal.
	 *
	 * @param MShop_Order_Item_Base_Interface $orderBase Order base item
	 * @return array Associative list of key/value pairs with order data required by PayPal
	 */
	protected function _getOrderDetails( MShop_Order_Item_Base_Interface $orderBase )
	{
		$values = $this->_getAuthParameter();

		try
		{
			$orderAddressDelivery = $orderBase->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );

			/* setting up the shipping address details (ReviewOrder) */
			$values['ADDROVERRIDE'] = 1;
			$values['PAYMENTREQUEST_0_SHIPTONAME'] = $orderAddressDelivery->getFirstName() . ' ' . $orderAddressDelivery->getLastName();
			$values['PAYMENTREQUEST_0_SHIPTOSTREET'] = $orderAddressDelivery->getAddress1() . ' ' . $orderAddressDelivery->getAddress2() . ' ' . $orderAddressDelivery->getAddress3();
			$values['PAYMENTREQUEST_0_SHIPTOCITY'] = $orderAddressDelivery->getCity();
			$values['PAYMENTREQUEST_0_SHIPTOSTATE'] = $orderAddressDelivery->getState();
			$values['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $orderAddressDelivery->getCountryId();
			$values['PAYMENTREQUEST_0_SHIPTOZIP'] = $orderAddressDelivery->getPostal();
		}
		catch( Exception $e ) { ; } // If no address is available

		$lastPos = 0;
		foreach( $orderBase->getProducts() as $product )
		{
			$lastPos = $product->getPosition() - 1;
			$values[ 'L_PAYMENTREQUEST_0_NUMBER' . $lastPos ] = $product->getId();
			$values[ 'L_PAYMENTREQUEST_0_NAME' . $lastPos ] = $product->getName();
			$values[ 'L_PAYMENTREQUEST_0_QTY' . $lastPos ] = $product->getQuantity();
			$values[ 'L_PAYMENTREQUEST_0_AMT' . $lastPos ] = $product->getPrice()->getValue();
		}

		foreach( $orderBase->getServices() as $service )
		{
			if( ( $val = $service->getPrice()->getValue() ) > '0.00' )
			{
				$lastPos++;
				$values[ 'L_PAYMENTREQUEST_0_NAME' . $lastPos ] = $service->getName();
				$values[ 'L_PAYMENTREQUEST_0_QTY' . $lastPos ] = '1';
				$values[ 'L_PAYMENTREQUEST_0_AMT' . $lastPos ] = $val;
			}
		}

		$paymentItem = $orderBase->getService('payment');
		if( ( $paymentCosts = $paymentItem->getPrice()->getCosts() ) > '0.00' )
		{
			$lastPos++;
			$values[ 'L_PAYMENTREQUEST_0_NAME' . $lastPos ] = $this->_getContext()->getI18n()->dt( 'mshop', 'Payment costs' );
			$values[ 'L_PAYMENTREQUEST_0_QTY' . $lastPos ] = '1';
			$values[ 'L_PAYMENTREQUEST_0_AMT' . $lastPos ] = $paymentCosts;
		}

		$price = $orderBase->getPrice();
		$amount = $price->getValue() + $price->getCosts();

		$values['MAXAMT'] = $amount + 0.01; // @todo rounding error?
		$values['PAYMENTREQUEST_0_AMT'] = number_format( $amount, 2, '.', '' );
		$values['PAYMENTREQUEST_0_ITEMAMT'] = ( string ) ( $price->getValue() + $paymentCosts );
		$values['PAYMENTREQUEST_0_SHIPPINGAMT'] = (string) ( $price->getCosts() - $paymentCosts );
		$values['PAYMENTREQUEST_0_INSURANCEAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED'] = 'false';
		$values['PAYMENTREQUEST_0_SHIPDISCAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_TAXAMT'] = $price->getTaxRate();
		$values['PAYMENTREQUEST_0_CURRENCYCODE'] = $orderBase->getPrice()->getCurrencyId();
		$values['PAYMENTREQUEST_0_PAYMENTACTION'] = $this->_getConfigValue( array( 'paypalexpress.PaymentAction' ), 'sale' );

		try
		{
			$orderServiceDeliveryItem = $orderBase->getService('delivery');

			$values['L_SHIPPINGOPTIONAMOUNT0'] = (string) ( $price->getCosts() - $paymentCosts );
			$values['L_SHIPPINGOPTIONLABEL0'] = $orderServiceDeliveryItem->getName();
			$values['L_SHIPPINGOPTIONNAME0'] = $orderServiceDeliveryItem->getCode();
			$values['L_SHIPPINGOPTIONISDEFAULT0'] = 'true';
		}
		catch( Exception $e ) { ; } // If no delivery service is available


		return $values;
	}


	/**
	 * Returns the data required for authorization against the PayPal server.
	 *
	 * @return array Associative list of key/value pairs containing the autorization parameters
	 */
	protected function _getAuthParameter()
	{
		return array(
			'VERSION' => '87.0',
			'SIGNATURE' => $this->_getConfigValue( array( 'paypalexpress.ApiSignature' ) ),
			'USER' => $this->_getConfigValue( array( 'paypalexpress.ApiUsername' ) ),
			'PWD' => $this->_getConfigValue( array( 'paypalexpress.ApiPassword' ) ),
		);
	}


	/**
	 * Returns order service item for specified base ID.
	 *
	 * @param integer $baseid Base ID of the order
	 * @return MShop_Order_Item_Base_Service_Interface Order service item
	 */
	protected function _getOrderServiceItem( $baseid )
	{
		$basket = $this->_getOrderBase( $baseid, MShop_Order_Manager_Base_Abstract::PARTS_SERVICE );
		return $basket->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );
	}
}