<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for reduction of service providers prices.
 *
 * @package MShop
 * @subpackage Service
 */
class Reduction
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
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
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		$list = $this->getProvider()->getConfigBE();

		foreach( $this->beConfig as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$config = $this->getServiceItem()->getConfig();

		$price = $this->getProvider()->calcPrice( $basket );
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