<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Decorator for reduction of service providers prices.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Reduction
extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'reduction.percent' => array(
			'code' => 'reduction.percent',
			'internalcode'=> 'reduction.percent',
			'label'=> 'Percent: Decimal value in percent (positive or negative)',
			'type'=> 'number',
			'internaltype'=> 'number',
			'default'=> 0,
			'required'=> false,
		),
		'reduction.basket-value-min' => array(
			'code' => 'reduction.basket-value-min',
			'internalcode'=> 'reduction.basket-value-min',
			'label'=> 'Percent: Minimum basket value required before increasing/decreasing costs',
			'type'=> 'map',
			'internaltype'=> 'map',
			'default'=> 0,
			'required'=> false,
		),
		'reduction.basket-value-max' => array(
			'code' => 'reduction.basket-value-max',
			'internalcode'=> 'reduction.basket-value-max',
			'label'=> 'Percent: Maximum basket value required until increasing/decreasing costs',
			'type'=> 'map',
			'internaltype'=> 'map',
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
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return MShop_Price_Item_Interface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( MShop_Order_Item_Base_Interface $basket )
	{
		$config = $this->getServiceItem()->getConfig();

		$price = $this->_getProvider()->calcPrice( $basket );
		$total = $basket->getPrice()->getValue() + $basket->getPrice()->getRebate();
		$currency = $price->getCurrencyId();

		if( isset( $config['reduction.basket-value-min'][$currency] )
			&& $total < $config['reduction.basket-value-min'][$currency]
		) {
			return $price;
		}

		if( isset( $config['reduction.basket-value-max'][$currency] )
			&& $total > $config['reduction.basket-value-max'][$currency]
		) {
			return $price;
		}

		if( isset( $config['reduction.percent'] ) )
		{
			$reduction = $price->getCosts() * $config['reduction.percent'] / 100;
			$price->setRebate( $price->getRebate() + $reduction );
			$price->setCosts( $price->getCosts() - $reduction );
		}

		return $price;
	}
}