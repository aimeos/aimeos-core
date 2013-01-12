<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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
extends MShop_Service_Provider_Payment_Abstract
implements MShop_Service_Provider_Payment_Interface
{
	private $_config;

	private $_beConfig = array(
		'url' => array(
			'code' => 'url',
			'internalcode'=> 'url',
			'label'=> 'URL to success page',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true,
		),
	);

	private $_feConfig = array(
		'payment.directdebit.accountowner' => array(
			'code' => 'payment.directdebit.accountowner',
			'internalcode'=> 'accountowner',
			'label'=> 'Account owner',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'payment.directdebit.accountnumber' => array(
			'code' => 'payment.directdebit.accountnumber',
			'internalcode'=> 'accountnumber',
			'label'=> 'Account number',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'payment.directdebit.bankcode' => array(
			'code' => 'payment.directdebit.bankcode',
			'internalcode'=> 'bankcode',
			'label'=> 'Bank code',
			'type'=> 'string',
			'internaltype'=> 'string',
			'default'=> '',
			'required'=> true
		),
		'payment.directdebit.bankname' => array(
			'code' => 'payment.directdebit.bankname',
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
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE()
	{
		$list = array();

		foreach( $this->_feConfig as $key => $config ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->_checkConfig( $this->_beConfig, $attributes );
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
		return $this->_checkConfig( $this->_feConfig, $attributes );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MW_Common_Form_Interface Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider)
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_AUTHORIZED );

		return new MShop_Common_Item_Helper_Form_Default( '', 'GET', array() );
	}
}