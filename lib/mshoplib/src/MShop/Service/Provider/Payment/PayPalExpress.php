<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: PayPalExpress.php 1170 2012-08-29 12:22:00Z doleiynyk $
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
	private $_config;

	private $_beConfig = array(
		'ApiUsername' => array(
			'code' => 'ApiUsername',
			'internalcode'=> 'username',
			'label'=> 'Username',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'ApiPassword' => array(
			'code' => 'ApiPassword',
			'internalcode'=> 'password',
			'label'=> 'Password',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'ApiSignature' => array(
			'code' => 'ApiSignature',
			'internalcode'=> 'signature',
			'label'=> 'Signature',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'CancelUrl' => array(
			'code' => 'CancelUrl',
			'internalcode'=> 'cancelurl',
			'label'=> 'CancelUrl',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'ReturnUrl' => array(
			'code' => 'ReturnUrl',
			'internalcode'=> 'returnurl',
			'label'=> 'ReturnUrl',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'PaymentAction' => array(
			'code' => 'PaymentAction',
			'internalcode'=> 'paymentaction',
			'label'=> 'PaymentAction',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'sale',
			'required'=> true,
		),
		'PaypalUrl' => array(
			'code' => 'PaypalUrl',
			'internalcode'=> 'paypalurl',
			'label'=> 'PaypalUrl',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'ApiEndpoint' => array(
			'code' => 'ApiEndpoint',
			'internalcode'=> 'apiendpoint',
			'label'=> 'APIEndpoint',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
	);


	public function __construct( MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem )
	{
		parent::__construct( $context, $serviceItem );

		$configParameters = array(
			'ApiUsername',
			'ApiPassword',
			'ApiSignature',
			'ApiEndpoint',
			'PaypalUrl',
			'ReturnUrl',
			'CancelUrl',
			'PaymentAction'
		);

		$this->_config = $serviceItem->getConfig();

		foreach( $configParameters as $param )
		{
			if( !isset( $this->_config[ $param ] ) ) {
				throw new MShop_Service_Exception( sprintf( 'Parameter "%1$s" for configuration not available', $param ) );
			}
		}
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
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->_checkConfig( $this->_beConfig, $attributes );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MShop_Common_Item_Helper_Form_Interface|null Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider) or null to redirect directly to the confirmation page
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$orderBaseItem = $orderBaseManager->load( $order->getBaseId() );

		$values = $this->_getOrderDetails( $orderBaseItem );
		$values['METHOD'] = 'SetExpressCheckout';
		$values[ 'PAYMENTREQUEST_0_INVNUM' ] = $order->getId();

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );

		$params = array ( 'TOKEN' => $rvals['TOKEN'] );
		$this->_saveAttributes( $params, $orderBaseItem->getService('payment') );

		return new MShop_Common_Item_Helper_Form_Default( $this->_config['PaypalUrl'] . $rvals['TOKEN'], 'GET', array() );
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

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
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
		$values['AMT'] = $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getShipping();

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
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

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
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

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
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
		if( !isset( $additional['TOKEN'] ) ) {
			return null;
		}


		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'GetExpressCheckoutDetails';
		$values['TOKEN'] = $additional['TOKEN'];

		$urlQuery = '&' . http_build_query( $values, '', '&' );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );

		$fullResponse = $this->_checkResponse( $additional['TOKEN'], $response, __METHOD__ );


		if( !isset( $fullResponse['PAYERID'] ) ) {
			throw new MShop_Service_Exception( sprintf( 'Paypal express user was not authorized' ) );
		}


		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');

		$order = $orderManager->getItem( $fullResponse['INVNUM'] );
		$baseid = $order->getBaseId();
		$baseItem = $orderBaseManager->getItem( $baseid );
		$serviceItem = $this->_getOrderServiceItem( $baseid );

		$values = $this->_getAuthParameter();
		$values['METHOD'] = 'DoExpressCheckoutPayment';
		$values['TOKEN'] = $fullResponse['TOKEN'];
		$values['PAYERID'] = $fullResponse['PAYERID'];
		$values['PAYMENTACTION'] = $this->_config['PaymentAction'];
		$values['CURRENCYCODE'] = $baseItem->getPrice()->getCurrencyId();
		$values['AMT'] = $amount = ( $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getShipping() );

		$urlQuery = urldecode( '&' . http_build_query( $values, '', '&' ) );
		$response = $this->_sendRequest( $this->_config['ApiEndpoint'], $urlQuery );
		$rvals = $this->_checkResponse( $order->getId(), $response, __METHOD__ );


		$attributes = array(
			'PAYERID' => $fullResponse['PAYERID'],
			'EMAIL' => $fullResponse['EMAIL']
		);

		if( isset( $rvals['TRANSACTIONID'] ) ) {
			$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
		}

		$this->_saveAttributes( $attributes, $serviceItem );
		$this->_setPaymentStatus( $order, $rvals );
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

		$cid = ( isset( $rvals['CORRELATIONID'] ) ? $rvals['CORRELATIONID'] : '<none>' );

		$logger = $this->_getContext()->getLogger();
		$logger->log( $method . ' : orderID=' . $orderid . ', CORRELATIONID=' . $cid, MW_Logger_Abstract::INFO );

		if( $rvals['ACK'] !== 'Success' )
		{
			if( $rvals['ACK'] !== 'SuccessWithWarning' )
			{
				throw new MShop_Service_Exception( sprintf(
						'Checking response from Paypal express payment server failed. Error "%1$s" occured for order ID "%2$s" (correlation ID: "%3$s"): %4$s',
						$rvals['L_ERRORCODE0'], $orderid, $cid, $rvals['L_SHORTMESSAGE0'] ) );
			}

			$str = $method . ' : orderID/token=' . $orderid . ', response=' . print_r( $rvals, true );
			$logger->log( $str, MW_Logger_Abstract::NOTICE );
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

			case 'None':
				$invoice->setPaymentStatus( MShop_Order_Item_Abstract::PAY_UNFINISHED );
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
			$values[ 'L_PAYMENTREQUEST_0_NUMBER' . $product->getPosition() ] = $product->getId();
			$values[ 'L_PAYMENTREQUEST_0_NAME' . $product->getPosition() ] = $product->getName();
			$values[ 'L_PAYMENTREQUEST_0_QTY' . $product->getPosition() ] = $product->getQuantity();
			$values[ 'L_PAYMENTREQUEST_0_AMT' . $product->getPosition() ] = $product->getPrice()->getValue();
			$lastPos = $product->getPosition();
		}

		foreach( $orderBase->getServices() as $service )
		{
			$lastPos++;
			$values[ 'L_PAYMENTREQUEST_0_NUMBER' . $lastPos ] = $service->getId();
			$values[ 'L_PAYMENTREQUEST_0_NAME' . $lastPos ] = $service->getName();
			$values[ 'L_PAYMENTREQUEST_0_QTY' . $lastPos ] = '1';
			$values[ 'L_PAYMENTREQUEST_0_AMT' . $lastPos ] = $service->getPrice()->getValue();
		}


		$price = $orderBase->getPrice();
		$amount = $price->getValue() + $price->getShipping();

		$values['MAXAMT'] = $amount + 0.01; // @todo rounding error?
		$values['PAYMENTREQUEST_0_AMT'] = number_format( $amount, 2, '.', '' );
		$values['PAYMENTREQUEST_0_ITEMAMT'] = $price->getValue();
		$values['PAYMENTREQUEST_0_SHIPPINGAMT'] = $price->getShipping();
		$values['PAYMENTREQUEST_0_INSURANCEAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED'] = 'false';
		$values['PAYMENTREQUEST_0_SHIPDISCAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_TAXAMT'] = $price->getTaxRate();
		$values['PAYMENTREQUEST_0_CURRENCYCODE'] = $orderBase->getPrice()->getCurrencyId();
		$values['PAYMENTREQUEST_0_PAYMENTACTION'] = $this->_config['PaymentAction'];
		$values['RETURNURL'] = $this->_config['ReturnUrl'];
		$values['CANCELURL'] = $this->_config['CancelUrl'];


		try
		{
			$orderServiceDeliveryItem = $orderBase->getService('delivery');

			$values['L_SHIPPINGOPTIONAMOUNT0'] = $price->getShipping();
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
			'SIGNATURE' => $this->_config['ApiSignature'],
			'USER' => $this->_config['ApiUsername'],
			'PWD' => $this->_config['ApiPassword'],
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