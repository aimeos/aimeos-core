<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2024
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
	private string $apiendpoint;

	private array $beConfig = array(
		'paypalexpress.ApiUsername' => array(
			'code' => 'paypalexpress.ApiUsername',
			'internalcode' => 'paypalexpress.ApiUsername',
			'label' => 'NVP API Username',
			'default' => '',
			'required' => true,
		),
		'paypalexpress.AccountEmail' => array(
			'code' => 'paypalexpress.AccountEmail',
			'internalcode' => 'paypalexpress.AccountEmail',
			'label' => 'Registered e-mail address of the shop owner in PayPal',
			'default' => '',
			'required' => true,
		),
		'paypalexpress.ApiPassword' => array(
			'code' => 'paypalexpress.ApiPassword',
			'internalcode' => 'paypalexpress.ApiPassword',
			'label' => 'NVP API Password',
			'default' => '',
			'required' => true,
		),
		'paypalexpress.ApiSignature' => array(
			'code' => 'paypalexpress.ApiSignature',
			'internalcode' => 'paypalexpress.ApiSignature',
			'label' => 'NVP API Signature',
			'default' => '',
			'required' => true,
		),
		'paypalexpress.ApiEndpoint' => array(
			'code' => 'paypalexpress.ApiEndpoint',
			'internalcode' => 'paypalexpress.ApiEndpoint',
			'label' => 'NVP API API Endpoint',
			'default' => 'https://api-3t.paypal.com/nvp',
			'required' => true,
		),
		'paypalexpress.PaypalUrl' => array(
			'code' => 'paypalexpress.PaypalUrl',
			'internalcode' => 'paypalexpress.PaypalUrl',
			'label' => 'NVP Express Checkout Url',
			'default' => 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
			'required' => true,
		),
		'paypalexpress.url-validate' => array(
			'code' => 'paypalexpress.url-validate',
			'internalcode' => 'paypalexpress.url-validate',
			'label' => 'NVP Validation URL',
			'default' => 'https://www.paypal.com/webscr&cmd=_notify-validate',
			'required' => true,
		),
		'paypalexpress.PaymentAction' => array(
			'code' => 'paypalexpress.PaymentAction',
			'internalcode' => 'paypalexpress.PaymentAction',
			'label' => 'How to obtain the payment: "Sale" (final sale), "Authorization" (basic authoriziation and capture) or "Order" (order authoriziation and capture)',
			'default' => 'Sale',
			'required' => true,
		),
		'paypalexpress.LandingPage' => array(
			'code' => 'paypalexpress.LandingPage',
			'internalcode' => 'paypalexpress.LandingPage',
			'label' => 'Type of displayed PayPal page: "Login" (PayPal login) or "Billing" (Non-PayPal account)',
			'default' => 'Login',
			'required' => false,
		),
		'paypalexpress.FundingSource' => array(
			'code' => 'paypalexpress.FundingSource',
			'internalcode' => 'paypalexpress.FundingSource',
			'label' => 'Preferred payment option: "CreditCard", "ELV", "ChinaUnionPay" or "QIWI" ("paypalexpress.LandingPage" must be set to "Billing")',
			'default' => 'CreditCard',
			'required' => false,
		),
		'paypalexpress.LocaleCode' => array(
			'code' => 'paypalexpress.LocaleCode',
			'internalcode' => 'paypalexpress.LocaleCode',
			'label' => 'ISO language code used at the PayPal page',
			'default' => '',
			'required' => false,
		),
		'paypalexpress.AddrOverride' => array(
			'code' => 'paypalexpress.AddrOverride',
			'internalcode' => 'paypalexpress.AddrOverride',
			'label' => 'Customer can change address',
			'type' => 'bool',
			'default' => 0,
			'required' => false,
		),
		'paypalexpress.NoShipping' => array(
			'code' => 'paypalexpress.NoShipping',
			'internalcode' => 'paypalexpress.NoShipping',
			'label' => 'Don\'t display shipping address',
			'type' => 'bool',
			'default' => 1,
			'required' => false,
		),
		'paypalexpress.address' => array(
			'code' => 'paypalexpress.address',
			'internalcode' => 'paypalexpress.address',
			'label' => 'Pass customer address to PayPal',
			'type' => 'bool',
			'default' => 1,
			'required' => false,
		),
		'paypalexpress.product' => array(
			'code' => 'paypalexpress.product',
			'internalcode' => 'paypalexpress.product',
			'label' => 'Pass product details to PayPal',
			'type' => 'bool',
			'default' => 1,
			'required' => false,
		),
		'paypalexpress.service' => array(
			'code' => 'paypalexpress.service',
			'internalcode' => 'paypalexpress.service',
			'label' => 'Pass delivery/payment details to PayPal',
			'type' => 'bool',
			'default' => 1,
			'required' => false,
		),
	);


	/**
	 * Initializes the provider object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration
	 * @throws \Aimeos\MShop\Service\Exception If one of the required configuration values isn't available
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		parent::__construct( $context, $serviceItem );

		$default = 'https://api-3t.paypal.com/nvp';
		$this->apiendpoint = $this->getConfigValue( array( 'paypalexpress.ApiEndpoint' ), $default );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
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
	 * @return \Aimeos\MShop\Common\Helper\Form\Iface|null Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order, array $params = [] ) : ?\Aimeos\MShop\Common\Helper\Form\Iface
	{
		$values = $this->getOrderDetails( $order );
		$values['METHOD'] = 'SetExpressCheckout';
		$values['PAYMENTREQUEST_0_INVNUM'] = $order->getId();
		$values['RETURNURL'] = $this->getConfigValue( array( 'payment.url-success' ) );
		$values['CANCELURL'] = $this->getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) );
		$values['USERSELECTEDFUNDINGSOURCE'] = $this->getConfigValue( array( 'paypalexpress.FundingSource' ), 'CreditCard' );
		$values['LANDINGPAGE'] = $this->getConfigValue( array( 'paypalexpress.LandingPage' ), 'Login' );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$default = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s';
		$paypalUrl = sprintf( $this->getConfigValue( array( 'paypalexpress.PaypalUrl' ), $default ), $rvals['TOKEN'] );

		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$serviceItem = $this->getBasketService( $order, $type, $this->getServiceItem()->getCode() );
		$serviceItem->addAttributeItems( $this->attributes( ['TOKEN' => $rvals['TOKEN']], 'tx' ) );

		return new \Aimeos\MShop\Common\Helper\Form\Standard( $paypalUrl, 'POST', [] );
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function query( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( ( $tid = $this->getOrderServiceItem( $order )->getAttribute( 'TRANSACTIONID', 'tx' ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $order->getId() ) );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'GetTransactionDetails';
		$values['TRANSACTIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		return $this->setStatusPayment( $order, $rvals );
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function capture( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$serviceItem = $this->getBasketService( $order, $type, $this->getServiceItem()->getCode() );

		if( ( $tid = $serviceItem->getAttribute( 'TRANSACTIONID', 'tx' ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $order->getId() ) );
		}

		$price = $order->getPrice();

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoCapture';
		$values['COMPLETETYPE'] = 'Complete';
		$values['AUTHORIZATIONID'] = $tid;
		$values['INVNUM'] = $order->getId();
		$values['CURRENCYCODE'] = $price->getCurrencyId();
		$values['AMT'] = $this->getAmount( $price );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$this->setStatusPayment( $order, $rvals );

		$attributes = [];
		if( isset( $rvals['PARENTTRANSACTIONID'] ) ) {
			$attributes['PARENTTRANSACTIONID'] = $rvals['PARENTTRANSACTIONID'];
		}

		// updates the transaction id
		$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
		$serviceItem->addAttributeItems( $this->attributes( $attributes, 'tx' ) );

		return $order;
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @param \Aimeos\MShop\Price\Item\Iface|null $price Price item with the amount to refund or NULL for whole order
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function refund( \Aimeos\MShop\Order\Item\Iface $order, ?\Aimeos\MShop\Price\Item\Iface $price = null
		) : \Aimeos\MShop\Order\Item\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$serviceItem = $this->getBasketService( $order, $type, $this->getServiceItem()->getCode() );

		if( ( $tid = $serviceItem->getAttribute( 'TRANSACTIONID', 'tx' ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $order->getId() ) );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'RefundTransaction';
		$values['REFUNDSOURCE'] = 'instant';
		$values['REFUNDTYPE'] = 'Full';
		$values['TRANSACTIONID'] = $tid;
		$values['INVOICEID'] = $order->getId();

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $order->getId(), $response, __METHOD__ );

		$attributes = array( 'REFUNDTRANSACTIONID' => $rvals['REFUNDTRANSACTIONID'] );
		$serviceItem->addAttributeItems( $this->attributes( $attributes, 'tx' ) );

		return $order->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_REFUND );
	}


	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	public function cancel( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( ( $tid = $this->getOrderServiceItem( $order )->getAttribute( 'TRANSACTIONID', 'tx' ) ) === null )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Payment transaction ID for order ID "%1$s" not available' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $order->getId() ) );
		}

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoVoid';
		$values['AUTHORIZATIONID'] = $tid;

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$this->checkResponse( $order->getId(), $response, __METHOD__ );

		return $order->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
	}


	/**
	 * Updates the order status sent by payment gateway notifications
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Response object
	 */
	public function updatePush( \Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		$params = $request->getQueryParams();

		if( !isset( $params['txn_id'] ) ) { //tid from ipn
			return $response->withStatus( 400, 'PayPal Express: Parameter "txn_id" is missing' );
		}

		$urlQuery = http_build_query( $params, '', '&' );

		//validation
		$result = $this->send( $this->getConfigValue( array( 'paypalexpress.url-validate' ) ), 'POST', $urlQuery );

		if( $result !== 'VERIFIED' ) {
			return $response->withStatus( 400, sprintf( 'PayPal Express: Invalid request "%1$s"', $urlQuery ) );
		}


		$manager = \Aimeos\MShop::create( $this->context(), 'order' );
		$order = $manager->get( $params['invoice'], ['order/base', 'order/service'] );

		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$serviceItem = $this->getBasketService( $order, $type, $this->getServiceItem()->getCode() );

		$this->checkIPN( $order, $params );

		$status = array( 'PAYMENTSTATUS' => $params['payment_status'] );

		if( isset( $params['pending_reason'] ) ) {
			$status['PENDINGREASON'] = $params['pending_reason'];
		}

		$serviceItem->addAttributeItems( $this->attributes( ['TRANSACTIONID' => $params['txn_id']], 'tx' ) )
			->addAttributeItems( $this->attributes( [$params['txn_id'] => $params['payment_status']], 'paypal/txn' ) );

		$manager->save( $this->setStatusPayment( $order, $status ) );

		return $response->withStatus( 200 );
	}


	/**
	 * Updates the orders for whose status updates have been received by the confirmation page
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object with parameters and request body
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item that should be updated
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 * @throws \Aimeos\MShop\Service\Exception If updating the orders failed
	 */
	public function updateSync( \Psr\Http\Message\ServerRequestInterface $request,
		\Aimeos\MShop\Order\Item\Iface $orderItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		$params = (array) $request->getAttributes() + (array) $request->getParsedBody() + (array) $request->getQueryParams();

		if( !isset( $params['token'] ) )
		{
			$msg = sprintf( $this->context()->translate( 'mshop', 'Required parameter "%1$s" is missing' ), 'token' );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		if( !isset( $params['PayerID'] ) )
		{
			$msg = sprintf( $this->context()->translate( 'mshop', 'Required parameter "%1$s" is missing' ), 'PayerID' );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}

		$price = $orderItem->getPrice();
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$serviceItem = $this->getBasketService( $orderItem, $type, $this->getServiceItem()->getCode() );

		$values = $this->getAuthParameter();
		$values['METHOD'] = 'DoExpressCheckoutPayment';
		$values['TOKEN'] = $params['token'];
		$values['PAYERID'] = $params['PayerID'];
		$values['PAYMENTACTION'] = $this->getConfigValue( array( 'paypalexpress.PaymentAction' ), 'Sale' );
		$values['CURRENCYCODE'] = $price->getCurrencyId();
		$values['AMT'] = $this->getAmount( $price );

		$urlQuery = http_build_query( $values, '', '&' );
		$response = $this->send( $this->apiendpoint, 'POST', $urlQuery );
		$rvals = $this->checkResponse( $orderItem->getId(), $response, __METHOD__ );

		$attributes = array( 'PAYERID' => $params['PayerID'] );

		if( isset( $rvals['TRANSACTIONID'] ) )
		{
			$attributes['TRANSACTIONID'] = $rvals['TRANSACTIONID'];
			$attrs = [$rvals['TRANSACTIONID'] => $rvals['PAYMENTSTATUS']];
			$serviceItem->addAttributeItems( $this->attributes( $attrs, 'paypal/txn' ) );
		}

		$serviceItem->addAttributeItems( $this->attributes( $attributes, 'tx' ) );
		return $this->setStatusPayment( $orderItem, $rvals );
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param int $what Constant from abstract class
	 * @return bool True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( int $what ) : bool
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
	 * Checks the response from the payment server.
	 *
	 * @param string $orderid Order item ID
	 * @param string $response Response from the payment provider
	 * @param string $method Name of the calling method
	 * @return array Associative list of key/value pairs containing the response parameters
	 * @throws \Aimeos\MShop\Service\Exception If request was not successful and an error was returned
	 */
	protected function checkResponse( string $orderid, string $response, string $method ) : array
	{
		$rvals = [];
		parse_str( $response, $rvals );

		if( $rvals['ACK'] !== 'Success' )
		{
			$msg = 'PayPal Express: method = ' . $method . ', order ID = ' . $orderid . ', response = ' . print_r( $rvals, true );
			$this->context()->logger()->warning( $msg, 'core/service/paypalexpress' );

			if( $rvals['ACK'] !== 'SuccessWithWarning' )
			{
				$short = ( isset( $rvals['L_SHORTMESSAGE0'] ) ? $rvals['L_SHORTMESSAGE0'] : '<none>' );
				$msg = $this->context()->translate( 'mshop', 'PayPal Express: Request for order ID "%1$s" failed with "%2$s"' );
				throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $orderid, $short ) );
			}
		}

		return $rvals;
	}


	/**
	 * Checks if IPN message from paypal is valid.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Order base item
	 * @param array $params List of parameters
	 * @return \Aimeos\MShop\Service\Provider\Payment\Iface Same object for fluent interface
	 */
	protected function checkIPN( \Aimeos\MShop\Order\Item\Iface $basket,
		array $params ) : \Aimeos\MShop\Service\Provider\Payment\Iface
	{
		$attrManager = \Aimeos\MShop::create( $this->context(), 'order/service/attribute' );

		if( $this->getConfigValue( array( 'paypalexpress.AccountEmail' ) ) !== $params['receiver_email'] )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Wrong receiver email "%1$s"' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $params['receiver_email'] ) );
		}

		$price = $basket->getPrice();

		if( $this->getAmount( $price ) != $params['payment_amount'] )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Wrong payment amount "%1$s" for order ID "%2$s"' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $params['payment_amount'], $params['invoice'] ) );
		}

		$search = $attrManager->filter();
		$expr = array(
			$search->compare( '==', 'order.service.attribute.code', $params['txn_id'] ),
			$search->compare( '==', 'order.service.attribute.value', $params['payment_status'] ),
		);

		$search->setConditions( $search->and( $expr ) );

		if( !$attrManager->search( $search )->isEmpty() )
		{
			$msg = $this->context()->translate( 'mshop', 'PayPal Express: Duplicate transaction with ID "%1$s" and status "%2$s"' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $params['txn_id'], $params['txn_status'] ) );
		}

		return $this;
	}


	/**
	 * Maps the PayPal status to the appropriate payment status and sets it in the order object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $invoice Order invoice object
	 * @param array $response Associative list of key/value pairs containing the PayPal response
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item object
	 */
	protected function setStatusPayment( \Aimeos\MShop\Order\Item\Iface $invoice, array $response ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( !isset( $response['PAYMENTSTATUS'] ) ) {
			return $invoice;
		}

		switch( $response['PAYMENTSTATUS'] )
		{
			case 'Pending':
				if( isset( $response['PENDINGREASON'] ) )
				{
					if( $response['PENDINGREASON'] === 'authorization' )
					{
						$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
						break;
					}

					$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', PENDINGREASON = ' . $response['PENDINGREASON'];
					$this->context()->logger()->info( $str, 'core/service/paypalexpress' );
				}

				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );
				break;

			case 'In-Progress':
				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );
				break;

			case 'Completed':
			case 'Processed':
				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
				break;

			case 'Failed':
			case 'Denied':
			case 'Expired':
				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_REFUSED );
				break;

			case 'Refunded':
			case 'Partially-Refunded':
			case 'Reversed':
				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_REFUND );
				break;

			case 'Canceled-Reversal':
			case 'Voided':
				$invoice->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
				break;

			default:
				$str = 'PayPal Express: order ID = ' . $invoice->getId() . ', response = ' . print_r( $response, true );
				$this->context()->logger()->info( $str, 'core/service/paypalexpress' );
		}

		return $invoice;
	}


	/**
	 * Returns an list of order data required by PayPal.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderBase Order base item
	 * @return array Associative list of key/value pairs with order data required by PayPal
	 */
	protected function getOrderDetails( \Aimeos\MShop\Order\Item\Iface $orderBase ) : array
	{
		$lastPos = 0;
		$deliveryPrices = [];
		$values = $this->getAuthParameter();
		$precision = $orderBase->getPrice()->getPrecision();


		if( $this->getConfigValue( 'paypalexpress.address', true ) )
		{
			if( ( $addresses = $orderBase->getAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY ) ) === [] ) {
				$addresses = $orderBase->getAddress( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT );
			}

			if( $address = current( $addresses ) )
			{
				/* setting up the address details */
				$values['NOSHIPPING'] = $this->getConfigValue( array( 'paypalexpress.NoShipping' ), 1 );
				$values['ADDROVERRIDE'] = $this->getConfigValue( array( 'paypalexpress.AddrOverride' ), 0 );
				$values['PAYMENTREQUEST_0_SHIPTONAME'] = $address->getFirstName() . ' ' . $address->getLastName();
				$values['PAYMENTREQUEST_0_SHIPTOSTREET'] = $address->getAddress1() . ' ' . $address->getAddress2() . ' ' . $address->getAddress3();
				$values['PAYMENTREQUEST_0_SHIPTOCITY'] = $address->getCity();
				$values['PAYMENTREQUEST_0_SHIPTOSTATE'] = $address->getState();
				$values['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $address->getCountryId();
				$values['PAYMENTREQUEST_0_SHIPTOZIP'] = $address->getPostal();
			}
		}

		$itemDeliveryCosts = 0;
		if( $this->getConfigValue( 'paypalexpress.product', true ) )
		{
			foreach( $orderBase->getProducts() as $product )
			{
				$price = $product->getPrice();
				$lastPos = $product->getPosition();

				$deliveryPrice = clone $price;
				$deliveryPrices = $this->addPrice( $deliveryPrices, $deliveryPrice->setValue( '0.00' ), $product->getQuantity() );

				$values['L_PAYMENTREQUEST_0_NUMBER' . $lastPos] = $product->getId();
				$values['L_PAYMENTREQUEST_0_NAME' . $lastPos] = $product->getName();
				$values['L_PAYMENTREQUEST_0_QTY' . $lastPos] = $product->getQuantity();
				$values['L_PAYMENTREQUEST_0_AMT' . $lastPos] = $this->getAmount( $price, false );
			}

			foreach( $deliveryPrices as $priceItem ) {
				$itemDeliveryCosts += $this->getAmount( $priceItem, true, true, $precision );
			}
		}


		if( $this->getConfigValue( 'paypalexpress.service', true ) )
		{
			foreach( $orderBase->getService( 'payment' ) as $service )
			{
				$price = $service->getPrice();

				if( ( $paymentCosts = $this->getAmount( $price ) ) > '0.00' )
				{
					$lastPos++;
					$values['L_PAYMENTREQUEST_0_NAME' . $lastPos] = $this->context()->translate( 'mshop', 'Payment costs' );
					$values['L_PAYMENTREQUEST_0_QTY' . $lastPos] = '1';
					$values['L_PAYMENTREQUEST_0_AMT' . $lastPos] = $paymentCosts;
				}
			}

			try
			{
				$lastPos = 0;
				foreach( $orderBase->getService( 'delivery' ) as $service )
				{
					$deliveryPrices = $this->addPrice( $deliveryPrices, $service->getPrice() );

					$values['L_SHIPPINGOPTIONAMOUNT' . $lastPos] = number_format( $service->getPrice()->getCosts() + $itemDeliveryCosts, $precision, '.', '' );
					$values['L_SHIPPINGOPTIONLABEL' . $lastPos] = $service->getCode();
					$values['L_SHIPPINGOPTIONNAME' . $lastPos] = $service->getName();
					$values['L_SHIPPINGOPTIONISDEFAULT' . $lastPos] = 'true';

					$lastPos++;
				}
			}
			catch( \Exception $e ) { ; } // If no delivery service is available
		}


		$deliveryCosts = 0;
		$price = $orderBase->getPrice();
		$amount = $this->getAmount( $price );

		foreach( $deliveryPrices as $priceItem ) {
			$deliveryCosts += $this->getAmount( $priceItem, true, true, $precision );
		}

		$values['MAXAMT'] = $amount + 1 / pow( 10, $precision ); // possible rounding error
		$values['PAYMENTREQUEST_0_AMT'] = $amount;
		$values['PAYMENTREQUEST_0_ITEMAMT'] = number_format( $amount - $deliveryCosts, $precision, '.', '' );
		$values['PAYMENTREQUEST_0_SHIPPINGAMT'] = number_format( $deliveryCosts, $precision, '.', '' );
		$values['PAYMENTREQUEST_0_INSURANCEAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED'] = 'false';
		$values['PAYMENTREQUEST_0_SHIPDISCAMT'] = '0.00';
		$values['PAYMENTREQUEST_0_CURRENCYCODE'] = $orderBase->getPrice()->getCurrencyId();
		$values['PAYMENTREQUEST_0_PAYMENTACTION'] = $this->getConfigValue( array( 'paypalexpress.PaymentAction' ), 'sale' );

		if( $localecode = $this->getConfigValue( 'paypalexpress.LocaleCode', null ) ) {
			$values['LOCALECODE'] = $localecode;
		}

		return $values;
	}


	/**
	 * Returns the data required for authorization against the PayPal server.
	 *
	 * @return array Associative list of key/value pairs containing the autorization parameters
	 */
	protected function getAuthParameter() : array
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
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order invoice object
	 * @return \Aimeos\MShop\Order\Item\Service\Iface Order service item
	 */
	protected function getOrderServiceItem( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Service\Iface
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		return $this->getBasketService( $order, $type, $this->getServiceItem()->getCode() );
	}


	/**
	 * Adds the costs to the price item with the corresponding tax rate
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface[] $prices Associative list of tax rates as key and price items as value
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item that should be added
	 * @param int $quantity Product quantity
	 * @return \Aimeos\MShop\Price\Item\Iface[] Updated list of price items
	 */
	protected function addPrice( array $prices, \Aimeos\MShop\Price\Item\Iface $price, int $quantity = 1 ) : array
	{
		$taxrate = $price->getTaxRate();

		if( !isset( $prices[$taxrate] ) )
		{
			$prices[$taxrate] = \Aimeos\MShop::create( $this->context(), 'price' )->create();
			$prices[$taxrate]->setTaxRate( $taxrate );
		}

		$prices[$taxrate]->addItem( $price, $quantity );

		return $prices;
	}


	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param string $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function send( string $target, string $method, string $payload ) : string
	{
		if( ( $curl = curl_init() ) === false ) {
			throw new \Aimeos\MShop\Service\Exception( 'Could not initialize curl' );
		}

		try
		{
			curl_setopt( $curl, CURLOPT_URL, $target );

			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, strtoupper( $method ) );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 25 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ); // return data as string

			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );

			if( ( $response = curl_exec( $curl ) ) === false )
			{
				$msg = $this->context()->translate( 'mshop', 'Sending order failed: "%1$s"' );
				throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, curl_error( $curl ) ) );
			}

			if( curl_errno( $curl ) )
			{
				$msg = $this->context()->translate( 'mshop', 'Curl error: "%1$s" - "%2$s"' );
				throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, curl_errno( $curl ), curl_error( $curl ) ) );
			}

			curl_close( $curl );
		}
		catch( \Exception $e )
		{
			curl_close( $curl );
			throw $e;
		}

		return $response;
	}
}
