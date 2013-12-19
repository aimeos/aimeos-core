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
		'paypal.Ipn' => array(
			'code' => 'paypal.Ipn',
			'internalcode'=> 'paypal.Ipn',
			'label'=> 'IPN',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'https://www.paypal.com/webscr&cmd=_notify-validate',
			'required'=> false,
		),
	);


	public function __construct( MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem )
	{
		parent::__construct( $context, $serviceItem );

		$configParameters = array(
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
	 * @return MW_Common_Form_Interface Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$orderBaseItem = $orderBaseManager->load( $order->getBaseId() );
		$orderid = $order->getId();

		$returnUrl = $this->_getConfigValue( array( 'payment.url-success' ) );
		$returnUrl .= ( strpos( $returnUrl, '?' ) !== false ? '&' : '?' ) . 'orderid=' . $orderid;

		$values = $this->_getOrderDetails( $orderBaseItem );
		$values[ 'METHOD' ] = 'SetExpressCheckout';
		$values[ 'PAYMENTREQUEST_0_INVNUM' ] = $orderid;
		$values[ 'RETURNURL' ] = $returnUrl;
		$values[ 'CANCELURL' ] = $this->_getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$default = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s';
		$paypalUrl = sprintf( $this->_getConfigValue( array( 'paypalexpress.PaypalUrl' ), $default ), $rvals['TOKEN'] );
		$this->_saveAttributes( array ( 'TOKEN' => $rvals['TOKEN'] ), $orderBaseItem->getService('payment') );

		return new MShop_Common_Item_Helper_Form_Default( $paypalUrl, 'POST', array() );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );

		if( ( $tid = $this->_getOrderServiceItem( $baseid )->getAttribute('TRANSACTIONID') ) === null )
		{
			$msg = sprintf( 'Paypal express payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'GetTransactionDetails';
		$values['TRANSACTIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$this->_setPaymentStatus( $order, $rvals );
		$orderManager->saveItem( $order );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function capture( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );
		$serviceItem = $this->_getOrderServiceItem( $baseid );

		if( ( $tid = $serviceItem->getAttribute('TRANSACTIONID') ) === null )
		{
			$msg = sprintf( 'Paypal express payment transaction ID for order ID "%1$s" not available', $order->getId() );
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
		$this->_saveAttributes( $attributes, $serviceItem );

		$orderManager->saveItem( $order );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function refund( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );
		$serviceItem = $this->_getOrderServiceItem( $baseid );

		if( ( $tid = $serviceItem->getAttribute('TRANSACTIONID') ) === null )
		{
			$msg = sprintf( 'Paypal express payment transaction ID for order ID "%1$s" not available', $order->getId() );
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
		$this->_saveAttributes( $attributes, $serviceItem );

		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUND );
		$orderManager->saveItem( $order );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );

		if( ( $tid = $this->_getOrderServiceItem( $baseid )->getAttribute('TRANSACTIONID') ) === null )
		{
			$msg = sprintf( 'Paypal express payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new MShop_Service_Exception( $msg );
		}

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoVoid';
		$values['AUTHORIZATIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_CANCELED );
		$orderManager->saveItem( $order );
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional )
	{
		if( isset( $additional['token'] ) && isset( $additional['PayerID'] ) && isset( $additional['orderid'] ) ) {
			return $this->_doExpressCheckoutPayment( $additional );
		}

		//tid from ipn
		if( !isset( $additional['txn_id'] ) ) {
			return null;
		}

		$urlQuery = http_build_query( $additional, '', '&' );

		//validation
		$response = $this->_getCommunication()->transmit( $this->_getConfigValue( array( 'paypal.Ipn' ) ), 'POST', $urlQuery );


		if( $response !== 'VERIFIED' )
		{
			$msg = sprintf( 'Error in PaypalExpress with validation request "%1$s"', $urlQuery );
			$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );

			return null;
		}

		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');
		$order = $orderManager->getItem( $additional['invoice'] );
		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );
		$serviceItem = $this->_getOrderServiceItem( $baseid );

		$status['PAYMENTSTATUS'] = $additional['payment_status'];
		$attributes['TRANSACTIONID'] = $additional['txn_id'];

		$this->_saveAttributes( $attributes, $serviceItem );
		$this->_setPaymentStatus( $order, $status );
		$orderManager->saveItem( $order );

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


	protected function _doExpressCheckoutPayment( $additional )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');

		$order = $orderManager->getItem( $additional['orderid'] );
		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );
		$serviceItem = $this->_getOrderServiceItem( $baseid );

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoExpressCheckoutPayment';
		$values['TOKEN'] = $additional['token'];
		$values['PAYERID'] = $additional['PayerID'];
		$values['PAYMENTACTION'] = $this->_getConfigValue( array( 'paypalexpress.PaymentAction' ), 'Sale' );
		$values['CURRENCYCODE'] = $baseItem->getPrice()->getCurrencyId();
		$values['NOTIFYURL'] = $this->_getConfigValue( array( 'payment.url-update', 'payment.url-success' ) );
		$values['AMT'] = $amount = ( $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getCosts() );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->_getCommunication()->transmit( $this->_apiendpoint, 'POST', $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'PAYERID' => $additional['PayerID'] );

		if( isset( $rvals['TRANSACTIONID'] ) ) {
			$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
		}

		$this->_saveAttributes( $attributes, $serviceItem );
		$this->_setPaymentStatus( $order, $rvals );
		$orderManager->saveItem( $order );

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
			$msg = sprintf( 'Error in Paypal express response for order with ID "%1$s": %2$s', $orderid, print_r( $rvals, true ) );
			$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::INFO );

			if( $rvals['ACK'] !== 'SuccessWithWarning' )
			{
				throw new MShop_Service_Exception( sprintf( $msg, $orderid,
					( isset( $rvals['L_SHORTMESSAGE0'] ) ? $rvals['L_SHORTMESSAGE0'] : '<none>' )
				) );
			}
		}

		return $rvals;
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

					$str = __METHOD__ . ' : orderID=' . $invoice->getId() . ', PENDINGREASON=' . $response['PENDINGREASON'];
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
				$str = __METHOD__ . ' : orderID=' . $invoice->getId() . ', response => ' . print_r( $response, true );
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
		catch( Exception $e ) { ; }

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

		$paymentCosts = '0.00';
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
		catch( Exception $e ) { ; }


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
	 * Saves a list of attributes for the order service.
	 *
	 * @param array $attributes Attributes which have to be saved
	 * @param MShop_Order_Item_Base_Serive_Interface $serviceItem Service Item which saves the attributes
	 */
	protected function _saveAttributes( array $attributes, MShop_Order_Item_Base_Service_Interface $serviceItem )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$attributeManager = $orderManager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$map = array();
		foreach( $serviceItem->getAttributes() as $attributeItem ) {
			$map[ $attributeItem->getCode() ] = $attributeItem;
		}

		foreach( $attributes as $code => $value )
		{
			if( array_key_exists( $code, $map ) !== true )
			{
				$attributeItem = $attributeManager->createItem();
				$attributeItem->setServiceId( $serviceItem->getId() );
				$attributeItem->setCode( $code );
				$attributeItem->setType( 'payment' );
			}
			else
			{
				$attributeItem = $map[$code];
			}

			$attributeItem->setValue( $value );
			$attributeManager->saveItem( $attributeItem );
		}
	}


	/**
	 * Returns order service item for specified base ID.
	 *
	 * @param integer $baseid Base ID of the order
	 * @return MShop_Order_Item_Base_Service_Interface Order service item
	 */
	protected function _getOrderServiceItem( $baseid )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderServiceManager = $orderManager->getSubManager( 'base' )->getSubManager( 'service' );

		$search = $orderServiceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.service.baseid', $baseid ),
			$search->compare( '==', 'order.base.service.type', 'payment' )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$results = $orderServiceManager->searchItems( $search );

		if ( ( $serviceItem = reset( $results ) ) === false )
		{
			$msg = sprintf( 'Service payment provider for order base ID "%1$s" not found', $baseid );
			throw new MShop_Service_Exception( $msg );
		}

		return $serviceItem;
	}
}