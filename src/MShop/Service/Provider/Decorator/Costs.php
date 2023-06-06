<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	private array $beConfig = array(
		'costs.percent' => array(
			'code' => 'costs.percent',
			'internalcode' => 'costs.percent',
			'label' => 'Costs: Decimal percent value',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => 0,
			'required' => true,
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
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket object
	 * @param array $options Selected options by customer from frontend
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Iface $basket, array $options = [] ) : \Aimeos\MShop\Price\Item\Iface
	{
		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['costs.percent'] ) )
		{
			$msg = $this->context()->translate( 'mshop', 'Missing configuration "%1$s"' );
			throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, 'costs.percent' ) );
		}

		$value = $basket->getPrice()->getValue() * $config['costs.percent'] / 100;
		$price = $this->getProvider()->calcPrice( $basket, $options );
		$price->setCosts( $price->getCosts() + $value );

		return $price;
	}
}
