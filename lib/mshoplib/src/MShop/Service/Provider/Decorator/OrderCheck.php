<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Decorator for service providers checking the orders of a customer.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_OrderCheck
extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'ordercheck.total-number-min' => array(
			'code' => 'ordercheck.total-number-min',
			'internalcode'=> 'ordercheck.total-number-min',
			'label'=> 'OrderCheck: Minimum successful orders',
			'type'=> 'integer',
			'internaltype'=> 'integer',
			'default'=> 0,
			'required'=> false,
		),
		'ordercheck.limit-days-pending' => array(
			'code' => 'ordercheck.limit-days-pending',
			'internalcode'=> 'ordercheck.limit-days-pending',
			'label'=> 'OrderCheck: Restrict if unpaid orders within time frame in days',
			'type'=> 'integer',
			'internaltype'=> 'integer',
			'default'=> 0,
			'required'=> false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$error = $this->_getProvider()->checkConfigBE( $attributes );
		$error += $this->_checkConfig( $this->_beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = $this->_getProvider()->getConfigBE();

		foreach( $this->_beConfig as $key => $config ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		$context = $this->_getContext();
		$config = $this->getServiceItem()->getConfig();

		if( ( $customerId = $context->getUserId() ) === null ) {
			return false;
		}

		$manager = MShop_Order_Manager_Factory::createManager( $context );

		if( isset( $config['ordercheck.total-number-min'] ) )
		{
			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'order.base.customerid', $customerId ),
				$search->compare( '>=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, $config['ordercheck.total-number-min'] );

			$result = $manager->searchItems( $search );

			if( count( $result ) < (int) $config['ordercheck.total-number-min'] ) {
				return false;
			}
		}

		if( isset( $config['ordercheck.limit-days-pending'] ) )
		{
			$time = time() - (int) $config['ordercheck.limit-days-pending'] * 86400;

			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'order.base.customerid', $customerId ),
				$search->compare( '==', 'order.datepayment', date( 'Y-m-d H:i:s', $time ) ),
				$search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_PENDING ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, 1 );

			$result = $manager->searchItems( $search );

			if( count( $result ) > 0 ) {
				return false;
			}
		}

		return $this->_getProvider()->isAvailable( $basket );
	}
}