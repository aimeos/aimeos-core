<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Payment provider for direct debit orders.
 *
 * @package MShop
 * @subpackage Service
 */
class DirectDebit
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{
	private $feConfig = array(
		'directdebit.accountowner' => array(
			'code' => 'directdebit.accountowner',
			'internalcode'=> 'accountowner',
			'label'=> 'Account owner',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.accountno' => array(
			'code' => 'directdebit.accountno',
			'internalcode'=> 'accountno',
			'label'=> 'Account number',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.bankcode' => array(
			'code' => 'directdebit.bankcode',
			'internalcode'=> 'bankcode',
			'label'=> 'Bank code',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'directdebit.bankname' => array(
			'code' => 'directdebit.bankname',
			'internalcode'=> 'bankname',
			'label'=> 'Bank name',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
	);


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$list = [];
		$feconfig = $this->feConfig;

		try
		{
			$address = $basket->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

			if( ( $fn = $address->getFirstname() ) !== '' && ( $ln = $address->getLastname() ) !== '' ) {
				$feconfig['directdebit.accountowner']['default'] = $fn . ' ' . $ln;
			}
		}
		catch( \Aimeos\MShop\Order\Exception $e ) { ; } // If address isn't available

		foreach( $feconfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes )
	{
		return $this->checkConfig( $this->feConfig, $attributes );
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

		if( ( $attrItem = $orderServiceItem->getAttributeItem( 'directdebit.accountno', 'payment' ) ) !== null )
		{
			$attrList = array( $attrItem->getCode() => $attrItem->getValue() );
			$this->setAttributes( $orderServiceItem, $attrList, 'payment/hidden' );

			$value = $attrItem->getValue();
			$len = strlen( $value );
			$xstr = ( $len > 3 ? str_repeat( 'X', $len - 3 ) : '' );

			$attrItem->setValue( $xstr . substr( $value, -3 ) );
			$orderServiceItem->setAttributeItem( $attrItem );
		}
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
		if( isset( $params['orderid'] ) )
		{
			$order = $this->getOrder( $params['orderid'] );
			$order->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED );
			$this->saveOrder( $order );

			return $order;
		}
	}
}