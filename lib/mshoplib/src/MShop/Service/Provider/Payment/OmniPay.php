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
		'omnipay.provider' => array(
			'code' => 'omnipay.provider',
			'internalcode'=> 'omnipay.provider',
			'label'=> 'Payment provider',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
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
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MShop_Common_Item_Helper_Form_Default Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$provider = $this->_getProvider();

		// off-site payment
		if( $provider->supportsCompletePurchase() || $provider->supportsCompleteAuthorize() ) {
			return $this->_processOffsite( $provider, $order );
		}

		return $this->_processOnsite( $provider, $order );
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @param string|null &$errmsg Error message shown to the user
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional, &$errmsg = null )
	{
		if( !isset( $additional['orderid'] ) || !isset( $additional['number'] )
			|| !isset( $additional['expiryMonth'] ) || !isset( $additional['expiryYear'] )
		) {
			return null;
		}

		$order = $this->_getOrder( $additional['orderid'] );
		$baseItem = $this->_getOrderBase( $order->getBaseId() );
		$serviceItem = $baseItem->getService( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$data = array(
			'transactionId' => $order->getId(),
			'amount' => $baseItem->getPrice()->getValue(),
			'currency' => $baseItem->getLocale()->getCurrencyId(),
			'card' => $additional,
		);

		$provider = $this->_getProvider();
		$response = $provider->purchase( $data )->send();

		if( $response->isSuccessful() )
		{
			$status = MShop_Order_Item_Abstract::PAY_RECEIVED;
			$attr = array( 'TRANSACTIONID' => $response->getTransactionReference() );

			$this->_setAttributes( $serviceItem, $attr, 'payment/omnipay' );
			$this->_saveOrderBase( $baseItem );
		}
		else
		{
			$status = MShop_Order_Item_Abstract::PAY_REFUSED;
			$errmsg = $response->getMessage();
		}

		$order->setPaymentStatus( $status );
		$this->_saveOrder( $order );

		return $order;
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
			$this->_provider = Omnipay::create( $this->_getConfigValue( array( 'omnipay.provider' ) ) );
			$this->_provider->initialize( $this->getServiceItem()->getConfig() );
		}

		return $this->_provider;
	}


	/**
	 * Process off-site payments where credit card data is entered and processed at the payment gateway.
	 *
	 * @param \Omnipay\Common\GatewayInterface $provider Gateway provider object
	 * @param MShop_Order_Item_Interface $order Order object
	 * @throws MShop_Service_Exception If URL for redirect to gateway site isn't available
	 * @return MShop_Common_Item_Helper_Form_Interface Form helper object
	 */
	protected function _processOffsite( \Omnipay\Common\GatewayInterface $provider, MShop_Order_Item_Interface $order )
	{
		$list = $carddata = array();
		$baseItem = $this->_getOrderBase( $order->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS );

		try
		{
			$address = $baseItem->getAddress();

			$carddata['firstName'] = $address->getFirstname();
			$carddata['lastName'] = $address->getLastname();
		}
		catch( MShop_Order_Exception $e ) { ; } // If address isn't available

		$data = array(
			'transactionId' => $order->getId(),
			'amount' => $baseItem->getPrice()->getValue(),
			'currency' => $baseItem->getLocale()->getCurrencyId(),
			'returnUrl' => $this->_getConfigValue( array( 'payment.url-success' ) ),
			'cancelUrl' => $this->_getConfigValue( array( 'payment.url-cancel', 'payment.url-success' ) ),
			'card' => $carddata,
		);

		try
		{
			$response = $provider->purchase( $data )->send();
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( 'Omnipay error: ' . $e->getMessage() );
			throw new MShop_Service_Exception( sprintf( 'An error occured during processing the payment' ) );
		}

		if( !$response->isRedirect() )
		{
			$msg = 'Redirect was expected for off-site credit card input: ';
			throw new MShop_Service_Exception( sprintf( $msg, $response->getMessage() ) );
		}

		foreach( $response->getRedirectData() as $key => $value )
		{
			$list[$key] = new MW_Common_Criteria_Attribute_Default( array(
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
	 * @param \Omnipay\Common\GatewayInterface $provider Gateway provider object
	 * @param MShop_Order_Item_Interface $order Order object
	 * @return MShop_Common_Item_Helper_Form_Interface Form helper object
	 */
	protected function _processOnsite( \Omnipay\Common\GatewayInterface $provider, MShop_Order_Item_Interface $order )
	{
		$list = array();
		$baseItem = $this->_getOrderBase( $order->getBaseId(), MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS );
		$confirmUrl = $this->_getConfigValue( array( 'payment.url-success' ) );

		try
		{
			$address = $baseItem->getAddress();

			$this->_feConfig['omnipay.firstname']['default'] = $address->getFirstname();
			$this->_feConfig['omnipay.lastname']['default'] = $address->getLastname();
		}
		catch( MShop_Order_Exception $e ) { ; } // If address isn't available

		$year = date( 'Y' );
		$this->_feConfig['omnipay.expirymonth']['default'] = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );
		$this->_feConfig['omnipay.expiryyear']['default'] = array( $year, $year+1, $year+2, $year+3, $year+4, $year+5, $year+6, $year+7 );

		foreach( $this->_feConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		$list['omnipay.orderid'] = new MW_Common_Criteria_Attribute_Default( array(
			'code' => 'omnipay.orderid',
			'internalcode'=> 'orderid',
			'label'=> 'Order ID',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> $order->getId(),
			'public'=> false,
		) );

		return new MShop_Common_Item_Helper_Form_Default( $confirmUrl, 'POST', $list, false );
	}
}
