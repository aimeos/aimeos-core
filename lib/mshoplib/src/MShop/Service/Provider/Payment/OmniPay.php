<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Service
 */


use Omnipay\Omnipay;


/**
 * Payment provider for payment providers supported by the Omnipay package.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Payment_OmniPay
	extends MShop_Service_Provider_Payment_Abstract
	implements MShop_Service_Provider_Payment_Interface
{
	private $_beConfig = array(
		'omnipay.type' => array(
			'code' => 'omnipay.type',
			'internalcode'=> 'omnipay.type',
			'label'=> 'Payment provider type',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
		'omnipay.capture' => array(
			'code' => 'omnipay.capture',
			'internalcode'=> 'omnipay.capture',
			'label'=> 'Capture payments immediately',
			'type'=> 'boolean',
			'internaltype'=> 'boolean',
			'default'=> '1',
			'required'=> false,
		),
		'omnipay.testmode' => array(
			'code' => 'omnipay.testmode',
			'internalcode'=> 'omnipay.testmode',
			'label'=> 'Test mode without payments',
			'type'=> 'boolean',
			'internaltype'=> 'boolean',
			'default'=> '0',
			'required'=> false,
		),
	);

	private $_feConfig = array(
		'omnipay.firstname' => array(
			'code' => 'omnipay.firstname',
			'internalcode'=> 'firstName',
			'label'=> 'First name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false
		),
		'omnipay.lastname' => array(
			'code' => 'omnipay.lastname',
			'internalcode'=> 'lastName',
			'label'=> 'Last name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'omnipay.cardno' => array(
			'code' => 'omnipay.cardno',
			'internalcode'=> 'number',
			'label'=> 'Credit card number',
			'type'=> 'number',
			'internaltype'=> 'integer',
			'default'=> '',
			'required'=> true
		),
		'omnipay.cvv' => array(
			'code' => 'omnipay.cvv',
			'internalcode'=> 'cvv',
			'label'=> 'Verification number',
			'type'=> 'number',
			'internaltype'=> 'integer',
			'default'=> '',
			'required'=> true
		),
		'omnipay.expirymonth' => array(
			'code' => 'omnipay.expirymonth',
			'internalcode'=> 'expiryMonth',
			'label'=> 'Expiry month',
			'type'=> 'select',
			'internaltype'=> 'integer',
			'default'=> '',
			'required'=> true
		),
		'omnipay.expiryyear' => array(
			'code' => 'omnipay.expiryyear',
			'internalcode'=> 'expiryYear',
			'label'=> 'Expiry year',
			'type'=> 'select',
			'internaltype'=> 'integer',
			'default'=> '',
			'required'=> true
		),
		'billing.company' => array(
			'code' => 'billing.company',
			'internalcode'=> 'company',
			'label'=> 'Company',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.address1' => array(
			'code' => 'billing.address1',
			'internalcode'=> 'billingAddress1',
			'label'=> 'Street',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.address2' => array(
			'code' => 'billing.address2',
			'internalcode'=> 'billingAddress2',
			'label'=> 'Additional',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.city' => array(
			'code' => 'billing.city',
			'internalcode'=> 'billingCity',
			'label'=> 'City',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.postal' => array(
			'code' => 'billing.postal',
			'internalcode'=> 'billingPostcode',
			'label'=> 'Zip code',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.state' => array(
			'code' => 'billing.state',
			'internalcode'=> 'billingState',
			'label'=> 'State',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.countryid' => array(
			'code' => 'billing.countryid',
			'internalcode'=> 'billingCountry',
			'label'=> 'Country',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.telephone' => array(
			'code' => 'billing.telephone',
			'internalcode'=> 'billingPhone',
			'label'=> 'Telephone',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
		'billing.email' => array(
			'code' => 'billing.email',
			'internalcode'=> 'email',
			'label'=> 'E-Mail',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> false,
			'public' => false,
		),
	);

	private $_provider;


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
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		$provider = $this->_getProvider();

		if( !$provider->supportsVoid() ) {
			return;
		}

		$base = $this->_getOrderBase( $order->getBaseId() );
		$service = $base->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$data = array(
			'transactionReference' => $service->getAttribute( 'TRANSACTIONID', 'payment/omnipay' ),
			'currency' => $base->getPrice()->getCurrencyId(),
			'amount' => $base->getPrice()->getValue(),
			'transactionId' => $order->getId(),
		);

		$response = $provider->void( $data )->send();

		if( $response->isSuccessful() )
		{
			$status = MShop_Order_Item_Abstract::PAY_CANCELED;
			$order->setPaymentStatus( $status );
			$this->_saveOrder( $order );
		}
	}


	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function capture( MShop_Order_Item_Interface $order )
	{
		$provider = $this->_getProvider();

		if( !$provider->supportsCapture() ) {
			return;
		}

		$base = $this->_getOrderBase( $order->getBaseId() );
		$service = $base->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$data = array(
			'transactionReference' => $service->getAttribute( 'TRANSACTIONID', 'payment/omnipay' ),
			'currency' => $base->getPrice()->getCurrencyId(),
			'amount' => $base->getPrice()->getValue(),
			'transactionId' => $order->getId(),
		);

		$response = $provider->capture( $data )->send();

		if( $response->isSuccessful() )
		{
			$status = MShop_Order_Item_Abstract::PAY_RECEIVED;
			$order->setPaymentStatus( $status );
		}
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		$provider = $this->_getProvider();

		switch( $what )
		{
			case MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE:
				return $provider->supportsCapture();
			case MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL:
				return $provider->supportsVoid();
			case MShop_Service_Provider_Payment_Abstract::FEAT_REFUND:
				return $provider->supportsRefund();
		}

		return false;
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MShop_Common_Item_Helper_Form_Default Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		// off-site payment
		if( $this->_getProvider()->supportsCompletePurchase() ) {
			return $this->_processOffsite( $order );
		}

		return $this->_processOnsite( $order );
	}


	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function refund( MShop_Order_Item_Interface $order )
	{
		$provider = $this->_getProvider();

		if( !$provider->supportsRefund() ) {
			return;
		}

		$base = $this->_getOrderBase( $order->getBaseId() );
		$service = $base->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$data = array(
			'transactionReference' => $service->getAttribute( 'TRANSACTIONID', 'payment/omnipay' ),
			'currency' => $base->getPrice()->getCurrencyId(),
			'amount' => $base->getPrice()->getValue(),
			'transactionId' => $order->getId(),
		);

		$response = $provider->refund( $data )->send();

		if( $response->isSuccessful() )
		{
			$attr = array( 'REFUNDID' => $response->getTransactionReference() );
			$this->_setAttributes( $serviceItem, $attr, 'payment/omnipay' );
			$this->_saveOrderBase( $baseItem );

			$status = MShop_Order_Item_Abstract::PAY_REFUND;
			$order->setPaymentStatus( $status );
			$this->_saveOrder( $order );
		}
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 */
	public function updateSync( array $params = array(), $body = null, &$response = null )
	{
		if( !isset( $params['orderid'] ) ) {
			return null;
		}

		// off-site payment
		if( $this->_getProvider()->supportsCompletePurchase() ) {
			return $this->_updateSyncOffsite( $params, $body, $response );
		}

		return $this->_updateSyncOnsite( $params, $body, $response );
	}


	/**
	 * Returns the IP address of the customer.
	 *
	 * @return string IP address of the customer
	 */
	protected function _getClientIP()
	{
		return '';
	}


	/**
	 * Returns the Omnipay gateway provider object.
	 *
	 * @return \Omnipay\Common\GatewayInterface Gateway provider object
	 */
	protected function _getProvider()
	{
		if( !isset( $this->_provider ) )
		{
			$this->_provider = Omnipay::create( $this->_getProviderType() );
			$this->_provider->setTestMode( (bool) $this->_getValue( 'testmode', false ) );
			$this->_provider->initialize( $this->getServiceItem()->getConfig() );
		}

		return $this->_provider;
	}


	/**
	 * Returns the Omnipay gateway provider name.
	 *
	 * @return string Gateway provider name
	 */
	protected function _getProviderType()
	{
		return $this->_getValue( 'type' );
	}


	/**
	 * Returns the required URLs
	 *
	 * @return array List of the Omnipay URL name as key and the URL string as value
	 */
	protected function _getPaymentUrls()
	{
		return array(
			'returnUrl' => $this->_getConfigValue( array( 'payment.url-success' ) ),
			'cancelUrl' => $this->_getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) ),
			'notifyUrl' => $this->_getConfigValue( array( 'payment.url-update' ) ),
		);
	}


	/**
	 * Returns the value for the given configuration key
	 *
	 * @param string $key Configuration key name
	 * @param mixed $default Default value if no configuration is found
	 * @return string Configuration value
	 */
	protected function _getValue( $key, $default = null )
	{
		return $this->_getConfigValue( array( 'omnipay.' . $key ), $default );
	}


	/**
	 * Process off-site payments where credit card data is entered and processed at the payment gateway.
	 *
	 * @param MShop_Order_Item_Interface $order Order object
	 * @throws MShop_Service_Exception If URL for redirect to gateway site isn't available
	 * @return MShop_Common_Item_Helper_Form_Interface Form helper object
	 */
	protected function _processOffsite( MShop_Order_Item_Interface $order )
	{
		$list = $carddata = array();
		$baseItem = $this->_getOrderBase( $order->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS );

		try
		{
			$address = $baseItem->getAddress();

			$carddata['firstName'] = $address->getFirstname();
			$carddata['lastName'] = $address->getLastname();

			if( $this->_getValue( 'address' ) )
			{
				$carddata['billingAddress1'] = $address->getAddress1();
				$carddata['billingAddress2'] = $address->getAddress2();
				$carddata['billingCity'] = $address->getCity();
				$carddata['billingPostcode'] = $address->getPostal();
				$carddata['billingState'] = $address->getState();
				$carddata['billingCountry'] = $address->getCountryId();
				$carddata['billingPhone'] = $address->getTelephone();
				$carddata['company'] = $address->getCompany();
				$carddata['email'] = $address->getEmail();
			}
		}
		catch( MShop_Order_Exception $e ) { ; } // If address isn't available

		$desc = $this->_getContext()->getI18n()->dt( 'mshop', 'Order %1$s' );

		$data = array(
			'token' => '',
			'card' => $carddata,
			'clientIp' => $this->_getClientIP(),
			'transactionId' => $order->getId(),
			'amount' => $baseItem->getPrice()->getValue(),
			'currency' => $baseItem->getLocale()->getCurrencyId(),
			'description' => sprintf( $desc, $order->getId() ),
		) + $this->_getPaymentUrls();

		try
		{
			$provider = $this->_getProvider();

			if( $provider->supportsAuthorize() && $this->_getValue( 'authorize', false ) ) {
				$response = $provider->authorize( $data )->send();
			} else {
				$response = $provider->purchase( $data )->send();
			}
		}
		catch( Exception $e )
		{
			throw new MShop_Service_Exception( $e->getMessage() );
		}

		if( !$response->isRedirect() )
		{
			$msg = 'Redirect was expected for off-site credit card input: %1$s';
			throw new MShop_Service_Exception( sprintf( $msg, $response->getRedirectUrl() ) );
		}

		foreach( (array) $response->getRedirectData() as $key => $value )
		{
			$list[$key] = new MW_Common_Criteria_Attribute_Default( array(
				'label' => $key,
				'code' => $key,
				'type' => 'string',
				'internalcode' => $key,
				'internaltype' => 'string',
				'default' => $value,
				'public' => false,
			) );
		}

		$url = $response->getRedirectUrl();
		$method = $response->getRedirectMethod();

		return new MShop_Common_Item_Helper_Form_Default( $url, $method, $list );
	}


	/**
	 * Process on-site payments where credit card data is entered and processed at the shop site.
	 *
	 * @param MShop_Order_Item_Interface $order Order object
	 * @return MShop_Common_Item_Helper_Form_Interface Form helper object
	 */
	protected function _processOnsite( MShop_Order_Item_Interface $order )
	{
		$list = array();
		$urls = $this->_getPaymentUrls();
		$baseItem = $this->_getOrderBase( $order->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS );

		try
		{
			$address = $baseItem->getAddress();

			$this->_feConfig['omnipay.firstname']['default'] = $address->getFirstname();
			$this->_feConfig['omnipay.lastname']['default'] = $address->getLastname();

			if( $this->_getValue( 'address' ) )
			{
				$this->_feConfig['billing.address1']['default'] = $address->getAddress1();
				$this->_feConfig['billing.address2']['default'] = $address->getAddress2();
				$this->_feConfig['billing.city']['default'] = $address->getCity();
				$this->_feConfig['billing.postal']['default'] = $address->getPostal();
				$this->_feConfig['billing.state']['default'] = $address->getState();
				$this->_feConfig['billing.country']['default'] = $address->getCountryId();
				$this->_feConfig['billing.telephone']['default'] = $address->getTelephone();
				$this->_feConfig['billing.company']['default'] = $address->getCompany();
				$this->_feConfig['billing.email']['default'] = $address->getEmail();
			}
		}
		catch( MShop_Order_Exception $e ) { ; } // If address isn't available

		$year = date( 'Y' );
		$this->_feConfig['omnipay.expirymonth']['default'] = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );
		$this->_feConfig['omnipay.expiryyear']['default'] = array( $year, $year+1, $year+2, $year+3, $year+4, $year+5, $year+6, $year+7 );

		foreach( $this->_feConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return new MShop_Common_Item_Helper_Form_Default( $urls['returnUrl'], 'POST', $list );
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @return MShop_Order_Item_Interface Order item if update was successful
	 */
	protected function _updateSyncOffsite( array $params, $body, &$response )
	{
		if( !isset( $params['orderid'] ) ) {
			return null;
		}

		$order = $this->_getOrder( $params['orderid'] );
		$baseItem = $this->_getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$params['transactionId'] = $order->getId();
		$params['amount'] = $baseItem->getPrice()->getValue();
		$params['currency'] = $baseItem->getLocale()->getCurrencyId();

		$provider = $this->_getProvider();

		if( $provider->supportsCompleteAuthorize() && $this->_getValue( 'authorize', false ) )
		{
			$response = $provider->completeAuthorize( $params )->send();
			$status = MShop_Order_Item_Abstract::PAY_AUTHORIZED;
		}
		else
		{
			$response = $provider->completePurchase( $params )->send();
			$status = MShop_Order_Item_Abstract::PAY_RECEIVED;
		}

		if( $response->isSuccessful() )
		{
			$attr = array( 'TRANSACTIONID' => $response->getTransactionReference() );
			$this->_setAttributes( $serviceItem, $attr, 'payment/omnipay' );
			$this->_saveOrderBase( $baseItem );

			$order->setPaymentStatus( $status );
			$this->_saveOrder( $order );
		}
		else
		{
			$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUSED );
			$this->_saveOrder( $order );

			throw new MShop_Service_Exception( $e->getMessage() );
		}

		return $order;
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param array $params Associative list of request parameters
	 * @param string|null $body Information sent within the body of the request
	 * @param string|null &$response Response body for notification requests
	 * @return MShop_Order_Item_Interface Order item if update was successful
	 */
	protected function _updateSyncOnsite( array $params, $body, &$response )
	{
		if( !isset( $params['orderid'] ) ) {
			return null;
		}

		$order = $this->_getOrder( $params['orderid'] );
		$baseItem = $this->_getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$desc = $this->_getContext()->getI18n()->dt( 'mshop', 'Order %1$s' );

		$data = array(
			'token' => '',
			'card' => $params,
			'clientIp' => $this->_getClientIP(),
			'transactionId' => $params['orderid'],
			'amount' => $baseItem->getPrice()->getValue(),
			'currency' => $baseItem->getLocale()->getCurrencyId(),
			'description' => sprintf( $desc, $order->getId() ),
		) + $this->_getPaymentUrls( $params['orderid'] );

		$provider = $this->_getProvider();

		if( $provider->supportsAuthorize() && $this->_getValue( 'authorize', false ) )
		{
			$response = $provider->authorize( $data )->send();
			$status = MShop_Order_Item_Abstract::PAY_AUTHORIZED;
		}
		else
		{
			$response = $provider->purchase( $data )->send();
			$status = MShop_Order_Item_Abstract::PAY_RECEIVED;
		}

		if( $response->isSuccessful() )
		{
			$attr = array( 'TRANSACTIONID' => $response->getTransactionReference() );
			$this->_setAttributes( $serviceItem, $attr, 'payment/omnipay' );
			$this->_saveOrderBase( $baseItem );

			$order->setPaymentStatus( $status );
			$this->_saveOrder( $order );
		}
		else
		{
			$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUSED );
			$this->_saveOrder( $order );

			throw new MShop_Service_Exception( $response->getMessage() );
		}

		return $order;
	}
}
