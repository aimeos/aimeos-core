<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * BasketValues decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class BasketValues
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'basketvalues.total-value-min' => array(
			'code' => 'basketvalues.total-value-min',
			'internalcode' => 'basketvalues.total-value-min',
			'label' => 'Minimum total value of the basket',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
		'basketvalues.total-value-max' => array(
			'code' => 'basketvalues.total-value-max',
			'internalcode' => 'basketvalues.total-value-max',
			'label' => 'Maximum total value of the basket',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Checks for the min/max order value.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return bool True if the basket matches the constraints, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		$price = $base->getPrice();
		$currency = $price->getCurrencyId();
		$value = $price->getValue() + $price->getRebate();

		$minvalue = $this->getConfigValue( 'basketvalues.total-value-min', [] );

		if( isset( $minvalue[$currency] ) && $minvalue[$currency] > $value ) {
			return false;
		}

		$maxvalue = $this->getConfigValue( 'basketvalues.total-value-max', [] );

		if( isset( $maxvalue[$currency] ) && $maxvalue[$currency] < $value ) {
			return false;
		}

		return parent::isAvailable( $base );
	}
}
