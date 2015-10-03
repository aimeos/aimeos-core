<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Payment provider for direct debit orders.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Payment_DirectDebit
	extends MShop_Service_Provider_Payment_Base
	implements MShop_Service_Provider_Payment_Interface
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
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE( MShop_Order_Item_Base_Interface $basket )
	{
		$list = array();
		$feconfig = $this->feConfig;

		try
		{
			$address = $basket->getAddress();

			if( ( $fn = $address->getFirstname() ) !== '' && ( $ln = $address->getLastname() ) !== '' ) {
				$feconfig['directdebit.accountowner']['default'] = $fn . ' ' . $ln;
			}
		}
		catch( MShop_Order_Exception $e ) { ; } // If address isn't available

		foreach( $feconfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
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
	 * @param MShop_Order_Item_Base_Service_Interface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
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
		$order->setPaymentStatus( MShop_Order_Item_Base::PAY_AUTHORIZED );
		$this->saveOrder( $order );

		return parent::process( $order, $params );
	}
}