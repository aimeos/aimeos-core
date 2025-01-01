<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for reduction of basket value.
 *
 * @package MShop
 * @subpackage Service
 */
class Reduction
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private array $beConfig = array(
		'reduction.percent' => array(
			'code' => 'reduction.percent',
			'internalcode' => 'reduction.percent',
			'label' => 'Decimal value in percent (positive or negative)',
			'type' => 'number',
			'default' => '',
			'required' => true,
		),
		'reduction.include-costs' => array(
			'code' => 'reduction.include-costs',
			'internalcode' => 'reduction.include-costs',
			'label' => 'Include delivery/payments costs in reduction calculation',
			'type' => 'bool',
			'default' => '0',
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
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Returns the price when using the provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @param array $options Selected options by customer from frontend
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Iface $basket, array $options = [] ) : \Aimeos\MShop\Price\Item\Iface
	{
		$price = $this->getProvider()->calcPrice( $basket, $options );

		if( $this->getConfigValue( 'reduction.include-costs' ) )
		{
			$sub = $price->getCosts() * $this->getConfigValue( 'reduction.percent' ) / 100;
			$price->setCosts( $price->getCosts() - $sub )->setRebate( $price->getRebate() + $sub );
		}

		$sub = $basket->getPrice()->getValue() * $this->getConfigValue( 'reduction.percent' ) / 100;
		return $price->setValue( $price->getValue() - $sub )->setRebate( $price->getRebate() + $sub );
	}
}
