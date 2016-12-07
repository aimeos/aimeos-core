<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers adding additional costs.
 *
 * @package MShop
 * @subpackage Service
 */
class Costs
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'costs.percent' => array(
			'code' => 'costs.percent',
			'internalcode'=> 'costs.percent',
			'label'=> 'Costs: Decimal percent value',
			'type'=> 'number',
			'internaltype'=> 'number',
			'default'=> 0,
			'required'=> true,
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

		if( !isset( $config['costs.percent'] ) ) {
			throw new \Aimeos\MShop\Service\Provider\Exception( sprintf( 'Missing configuration "%1$s"', 'costs.percent' ) );
		}

		$value = $basket->getPrice()->getValue() * $config['costs.percent'] / 100;
		$price = $this->getProvider()->calcPrice( $basket );
		$price->setCosts( $price->getCosts() + $value );

		return $price;
	}
}