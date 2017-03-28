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
 * Payment provider for paypal express orders.
 *
 * @package MShop
 * @subpackage Service
 */
class PayPalExpress
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{
	private $apiendpoint;

	private $beConfig = array(
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
		'paypalexpress.LandingPage' => array(
			'code' => 'paypalexpress.LandingPage',
			'internalcode'=> 'paypalexpress.LandingPage',
			'label'=> 'Landing page',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'Login',
			'required'=> false,
		),
		'paypalexpress.FundingSource' => array(
			'code' => 'paypalexpress.FundingSource',
			'internalcode'=> 'paypalexpress.FundingSource',
			'label'=> 'Funding source',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> 'CreditCard',
			'required'=> false,
		),
		'paypalexpress.AddrOverride' => array(
			'code' => 'paypalexpress.AddrOverride',
			'internalcode'=> 'paypalexpress.AddrOverride',
			'label'=> 'Customer can change address',
			'type'=> 'integer',
			'internaltype'=> 'integer',
			'default'=> 0,
			'required'=> false,
		),
		'paypalexpress.NoShipping' => array(
			'code' => 'paypalexpress.NoShipping',
			'internalcode'=> 'paypalexpress.NoShipping',
			'label'=> 'Don\'t display shipping address',
			'type'=> 'integer',
			'internaltype'=> 'integer',
			'default'=> 1,
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
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration
	 * @throws \Aimeos\MShop\Service\Exception If one of the required configuration values isn't available
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
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
			if( !isset( $config[$param] ) ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Configuration for "%1$s" is missing', $param ) );
			}
		}

		$default = 'https://api-3t.paypal.com/nvp';
		$this->apiendpoint = $this->getConfigValue( array( 'paypalexpress.ApiEndpoint' ), $default );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		$list = parent::getConfigBE();

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
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

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
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
		$orderid = $order->getId();
		$orderBaseItem = $this->getOrderBase( $order->getBaseId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );

		$values = $this->getOrderDetails( $orderBaseItem );
		$values['METHOD'] = 'SetExpressCheckout';
		$values['PAYMENTREQUEST_0_INVNUM'] = $orderid;
		$values['RETURNURL'] = $this->getConfigValue( array( 'payment.url-success' ) );
		$values['CANCELURL'] = $this->getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) );
		$values['USERSELECTEDFUNDINGSOURCE'] = $this->getConfigValue( array( 'paypalexpress.FundingSource' ), 'CreditCard' );
		$values['LANDINGPAGE'] = $this->getConfigValue( array( 'paypalexpress.LandingPage' ), 'Login' );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $orderid, $response, __METHOD__ );

		$default = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s';
		$paypalUrl = sprintf( $this->getConfigValue( array( 'paypalexpress.PaypalUrl' ), $default ), $rvals['TOKEN'] );

		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->setAttributes( $orderBaseItem->getService( $type ), array( 'TOKEN' => $rvals['TOKEN'] ), 'payment/paypal' );
		$this->saveOrderBase( $orderBaseItem );

		return new \Aimeos\MShop\Common\Item\Helper\Form\Standard( $paypalUrl, 'POST', [] );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order )
	{
		if( ( $tid = $this->getOrderServiceItem( $order->getBaseId() )->getAttribute( 'TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'GetTransactionDetails';
		$values['TRANSACTIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$this->setPaymentStatus( $order, $rvals );
		$this->saveOrder( $order );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$baseid = $order->getBaseId();
		$baseItem = $this->getOrderBase( $baseid );
		$serviceItem = $baseItem->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );

		if( ( $tid = $serviceItem->getAttribute( 'TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$price = $baseItem->getPrice();

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoCapture';
		$values['COMPLETETYPE'] = 'Complete';
		$values['AUTHORIZATIONID'] = $tid;
		$values['INVNUM'] = $order->getId();
		$values['CURRENCYCODE'] = $price->getCurrencyId();
		$values['AMT'] = $this->getAmount( $price );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$this->setPaymentStatus( $order, $rvals );

		$attributes = [];
		if( isset( $rvals['PARENTTRANSACTIONID'] ) ) {
			$attributes['PARENTTRANSACTIONID'] = $rvals['PARENTTRANSACTIONID'];
		}

		//updates the transaction id
		$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
		$this->setAttributes( $serviceItem, $attributes, 'payment/paypal' );

		$this->saveOrderBase( $baseItem );
		$this->saveOrder( $order );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$baseItem = $this->getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );

		if( ( $tid = $serviceItem->getAttribute( 'TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'RefundTransaction';
		$values['REFUNDSOURCE'] = 'instant';
		$values['REFUNDTYPE'] = 'Full';
		$values['TRANSACTIONID'] = $tid;
		$values['INVOICEID'] = $order->getId();

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'REFUNDTRANSACTIONID' => $rvals['REFUNDTRANSACTIONID'] );
		$this->setAttributes( $serviceItem, $attributes, 'payment/paypal' );
		$this->saveOrderBase( $baseItem );

		$order->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_REFUND );
		$this->saveOrder( $order );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order )
	{
		if( ( $tid = $this->getOrderServiceItem( $order->getBaseId() )->getAttribute( 'TRANSACTIONID', 'payment/paypal' ) ) === null )
		{
			$msg = sprintf( 'PayPal Express: Payment transaction ID for order ID "%1$s" not available', $order->getId() );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoVoid';
		$values['AUTHORIZATIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$this->checkResponse( $order->getId(), $response, __METHOD__ );

		$order->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
		$this->saveOrder( $order );
	}


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
		if( isset( $params['token'] ) && isset( $params['PayerID'] ) && isset( $params['orderid'] ) ) {
			return $this->doExpressCheckoutPayment( $params );
		}

		//tid from ipn
		if( !isset( $params['txn_id'] ) ) {
			return null;
		}

		$urlQuery = http_build_query( $params, '', '&' );

		//validation
		$response = $this->getCommunication()->transmit( $this->getConfigValue( array( 'paypalexpress.url-validate' ) ), 'POST', $urlQuery );


		if( $response !== 'VERIFIED' )
		{
			$msg = sprintf( 'PayPal Express: Invalid request "%1$s"', $urlQuery );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}


		$order = $this->getOrder( $params['invoice'] );
		$baseItem = $this->getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );

		$this->checkIPN( $baseItem, $params );

		$status = array( 'PAYMENTSTATUS' => $params['payment_status'] );

		if( isset( $params['pending_reason'] ) ) {
			$status['PENDINGREASON'] = $params['pending_reason'];
		}

		$this->setAttributes( $serviceItem, array( $params['txn_id'] => $params['payment_status'] ), 'payment/paypal/txn' );
		$this->setAttributes( $serviceItem, array( 'TRANSACTIONID' => $params['txn_id'] ), 'payment/paypal' );
		$this->saveOrderBase( $baseItem );

		$this->setPaymentStatus( $order, $status );
		$this->saveOrder( $order );

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
			case \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE:
			case \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY:
			case \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL:
			case \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_REFUND:
				return true;
		}

		return false;
	}

	/**
	 * Begins paypalexpress transaction and saves transaction id.
	 *
	 * @param mixed $params Update information whose format depends on the payment provider
	 * @return \Aimeos\MShop\Order\Item\Iface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 */
	protected function doExpressCheckoutPayment( $params )
	{
		$order = $this->getOrder( $params['orderid'] );
		$baseid = $order->getBaseId();
		$baseItem = $this->getOrderBase( $baseid );
		$serviceItem = $baseItem->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );

		$price = $baseItem->getPrice();

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoExpressCheckoutPayment';
		$values['TOKEN'] = $params['token'];
		$values['PAYERID'] = $params['PayerID'];
		$values['PAYMENTACTION'] = $this->getConfigValue( array( 'paypalexpress.PaymentAction' ), 'Sale' );
		$values['CURRENCYCODE'] = $price->getCurrencyId();
		$values['NOTIFYURL'] = $this->getConfigValue( array( 'payment.url-update', 'payment.url-success' ) );
		$values['AMT'] = $this->getAmount( $price );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->getCommunication()->transmit( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'PAYERID' => $params['PayerID'] );

		if( isset( $rvals['TRANSACTIONID'] ) )
		{
			$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
			$this->setAttributes( $serviceItem, array( $rvals['TRANSACTIONID'] => $rvals['PAYMENTSTATUS'] ), 'payment/paypal/txn' );
		}

		$this->setAttributes( $serviceItem, $attributes, 'payment/paypal' );
		$this->saveOrderBase( $baseItem );

		$this->setPaymentStatus( $order, $rvals );
		$this->saveOrder( $order );

		return $order;
	}


	/**
	 * Checks the response from the payment server.
	 *
	 * @param string $orderid Order item ID
	 * @param string $response Response from the payment provider
	 * @param string $method Name of the calling method
	 * @return array Associative list of key/value pairs containing the response parameters
	 * @throws \Aimeos\MShop\Service\Exception If request was not successful and an error was returned
	 */
	protected function checkResponse( $orderid, $response, $method )
	{
		$rvals = [];
		parse_str( $response, $rvals );

		if( $rvals['ACK'] !== 'Success' )
		{
			$msg = 'PayPal Express: method = ' . $method . ', order ID = ' . $orderid . ', response = ' . print_r( $rvals, true );
			$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );

			if( $rvals['ACK'] !== 'SuccessWithWarning' )
			{
				$short = ( isset( $rvals['L_SHORTMESSAGE0'] ) ? $rvals['L_SHORTMESSAGE0'] : '<none>' );
				$msg = sprintf( 'PayPal Express: Request for order ID "%1$s" failed with "%2$s"', $orderid, $short );
				throw new \Aimeos\MShop\Service\Exception( $msg );
			}
		}

		return $rvals;
	}


	/**
	 * Checks if IPN message from paypal is valid.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket
	 * @param array $params
	 */
	protected function checkIPN( $basket, $params )
	{
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order/base/service/attribute' );

		if( $this->getConfigValue( array( 'paypalexpress.AccountEmail' ) ) !== $params['receiver_email'] )
		{
			$msg = sprintf( 'PayPal Express: Wrong receiver email "%1$s"', $params['receiver_email'] );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$price = $basket->getPrice();

		if( $this->getAmount( $price ) != $params['payment_amount'] )
		{
			$msg = sprintf( 'PayPal Express: Wrong payment amount "%1$s" for order ID "%2$s"', $params['payment_amount'], $params['invoice'] );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.service.attribute.code', $params['txn_id'] ),
			$search->compare( '==', 'order.base.service.attribute.value', $params['payment_status'] ),
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $attrManager->searchItems( $search );

		if( ( $attr = reset( $results ) ) !== false )
		{
			$msg = sprintf( 'PayPal Express: Duplicate transaction with ID "%1$s" and status "%2$s" ', $params['txn_id'], $params['txn_status'] );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}
	}


	/**
	 * Maps the PayPal status to the appropriate payment status and sets it in the order object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $invoice Order invoice object
	 * @param array $response Associative list of key/value pairs containing the PayPal response
	 */
	protected function setPaymentStatus( \Aimeos\MShop\Order\Item\Iface $invoice, array $response )
	{
		if( !isset( $response['PAYMENTSTATUS'] ) ) {
			return;
		}

		switch( $response['PAYMENTSTATUS'] )
		{
			case 'Pending':
				if( isset( $response['PENDINGREASON'] ) )
				{
					if( $response['PENDINGREASON'] === 'authorization' )
					{
						$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
						break;
					}

					$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', PENDINGREASON = ' . $response['PENDINGREASON'];
					$this->getContext()->getLogger()->log( $str, \Aimeos\MW\Logger\Base::INFO );
				}

				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );
				break;

			case 'In-Progress':
				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );
				break;

			case 'Completed':
			case 'Processed':
				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
				break;

			case 'Failed':
			case 'Denied':
			case 'Expired':
				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_REFUSED );
				break;

			case 'Refunded':
			case 'Partially-Refunded':
			case 'Reversed':
				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_REFUND );
				break;

			case 'Canceled-Reversal':
			case 'Voided':
				$invoice->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
				break;

			default:
				$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', response = ' . print_r( $response, true );
				$this->getContext()->getLogger()->log( $str, \Aimeos\MW\Logger\Base::INFO );
		}
	}


	/**
	 * Returns an list of order data required by PayPal.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $orderBase Order base item
	 * @return array Associative list of key/value pairs with order data required by PayPal
	 */
	protected function getOrderDetails( \Aimeos\MShop\Order\Item\Base\Iface $orderBase )
	{
		$deliveryCosts = 0;
		$deliveryPrices = [];
		$values = $this->getAuthParameter();

		try
		{
			$orderAddressDelivery = $orderBase->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

			/* setting up the address details */
			$values['NOSHIPPING'] = $this->getConfigValue( array( 'paypalexpress.NoShipping' ), 1 );
			$values['ADDROVERRIDE'] = $this->getConfigValue( array( 'paypalexpress.AddrOverride' ), 0 );
			$values['PAYMENTREQUEST_0_SHIPTONAME'] = $orderAddressDelivery->getFirstName() . ' ' . $orderAddressDelivery->getLastName();
			$values['PAYMENTREQUEST_0_SHIPTOSTREET'] = $orderAddressDelivery->getAddress1() . ' ' . $orderAddressDelivery->getAddress2() . ' ' . $orderAddressDelivery->getAddress3();
			$values['PAYMENTREQUEST_0_SHIPTOCITY'] = $orderAddressDelivery->getCity();
			$values['PAYMENTREQUEST_0_SHIPTOSTATE'] = $orderAddressDelivery->getState();
			$values['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $orderAddressDelivery->getCountryId();
			$values['PAYMENTREQUEST_0_SHIPTOZIP'] = $orderAddressDelivery->getPostal();
		}
		catch( \Exception $e ) { ; } // If no address is available


		$lastPos = 0;
		foreach( $orderBase->getProducts() as $product )
		{
			$price = $product->getPrice();
			$lastPos = $product->getPosition() - 1;

			$deliveryPrice = clone $price;
			$deliveryPrices = $this->addPrice( $deliveryPrices, $deliveryPrice->setValue( '0.00' ) );

			$values['L_PAYMENTREQUEST_0_NUMBER' . $lastPos] = $product->getId();
			$values['L_PAYMENTREQUEST_0_NAME' . $lastPos] = $product->getName();
			$values['L_PAYMENTREQUEST_0_QTY' . $lastPos] = $product->getQuantity();
			$values['L_PAYMENTREQUEST_0_AMT' . $lastPos] = $this->getAmount( $price, false );
		}


		$price = $orderBase->getService( 'payment' )->getPrice();
		if( ( $paymentCosts = $this->getAmount( $price ) ) > '0.00' )
		{
			$lastPos++;
			$values['L_PAYMENTREQUEST_0_NAME' . $lastPos] = $this->getContext()->getI18n()->dt( 'mshop', 'Payment costs' );
			$values['L_PAYMENTREQUEST_0_QTY' . $lastPos] = '1';
			$values['L_PAYMENTREQUEST_0_AMT' . $lastPos] = $paymentCosts;
		}


		try
		{
			$orderServiceDeliveryItem = $orderBase->getService( 'delivery' );
			$price = $orderServiceDeliveryItem->getPrice();
			$deliveryPrices = $this->addPrice( $deliveryPrices, $price );

			foreach( $deliveryPrices as $priceItem ) {
				$deliveryCosts += $this->getAmount( $priceItem );
			}

			$values['L_SHIPPINGOPTIONAMOUNT0'] = number_format( $deliveryCosts, 2, '.', '' );
			$values['L_SHIPPINGOPTIONLABEL0'] = $orderServiceDeliveryItem->getCode();
			$values['L_SHIPPINGOPTIONNAME0'] = $orderServiceDeliveryItem->getName();
			$values['L_SHIPPINGOPTIONISDEFAULT0'] = 'true';
		}
		catch( \Exception $e ) { ; } // If no delivery service is available


		$price = $orderBase->getPrice();
		$amount = $this->getAmount( $price );

		if( $deliveryCosts === 0 )
		{
			foreach( $deliveryPrices as $priceItem ) {
				$deliveryCosts += $this->getAmount( $priceItem );
			}
		}

		$values['MAXAMT'] = $amount + 0.01; // possible rounding error
		$values['PAYMENTREQUEST_0_AMT'] = $amount;
		$values['PAYMENTREQUEST_0_ITEMAMT'] = number_format( $amount - $deliveryCosts, 2, '.', '' );
		$values['PAYMENTREQUEST_0_SHIPPINGAMT'] = number_format( $deliveryCosts, 2, '.', '' );
		$values['PAYMENTREQUEST_0_INSURANCEAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED'] = 'false';
		$values['PAYMENTREQUEST_0_SHIPDISCAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_CURRENCYCODE'] = $orderBase->getPrice()->getCurrencyId();
		$values['PAYMENTREQUEST_0_PAYMENTACTION'] = $this->getConfigValue( array( 'paypalexpress.PaymentAction' ), 'sale' );

		return $values;
	}


	/**
	 * Returns the data required for authorization against the PayPal server.
	 *
	 * @return array Associative list of key/value pairs containing the autorization parameters
	 */
	protected function getAuthParameter()
	{
		return array(
			'VERSION' => '204.0',
			'SIGNATURE' => $this->getConfigValue( array( 'paypalexpress.ApiSignature' ) ),
			'USER' => $this->getConfigValue( array( 'paypalexpress.ApiUsername' ) ),
			'PWD' => $this->getConfigValue( array( 'paypalexpress.ApiPassword' ) ),
		);
	}


	/**
	 * Returns order service item for specified base ID.
	 *
	 * @param integer $baseid Base ID of the order
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface Order service item
	 */
	protected function getOrderServiceItem( $baseid )
	{
		$basket = $this->getOrderBase( $baseid, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE );
		return $basket->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
	}


	/**
	 * Adds the costs to the price item with the corresponding tax rate
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface[] $prices Associative list of tax rates as key and price items as value
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item that should be added
	 * @return \Aimeos\MShop\Price\Item\Iface[] Updated list of price items
	 */
	protected function addPrice( array $prices, $price )
	{
		$taxrate = $price->getTaxRate();

		if( !isset( $prices[$taxrate] ) )
		{
			$prices[$taxrate] = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' )->createItem();
			$prices[$taxrate]->setTaxRate( $taxrate );
		}

		$prices[$taxrate]->addItem( $price );

		return $prices;
	}
}