<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
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
			'internalcode' => 'reduction.percent',
			'label' => 'Decimal value in percent (positive or negative)',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => '',
			'required' => true,
		),
		'reduction.product-costs' => array(
			'code' => 'reduction.product-costs',
			'internalcode' => 'reduction.product-costs',
			'label' => 'Include product costs in reduction calculation',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false,
		),
		'reduction.basket-value-min' => array(
			'code' => 'reduction.basket-value-min',
			'internalcode' => 'reduction.basket-value-min',
			'label' => 'Apply decorator over this basket value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
		'reduction.basket-value-max' => array(
			'code' => 'reduction.basket-value-max',
			'internalcode' => 'reduction.basket-value-max',
			'label' => 'Apply decorator up to this basket value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
		'reduction.product-costs' => array(
			'code' => 'reduction.product-costs',
			'internalcode' => 'reduction.product-costs',
			'label' => 'Include product shipping costs in reduction',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => 0,
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
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Price\Item\Iface
	{
		$price = $this->getProvider()->calcPrice( $basket );
		$total = $basket->getPrice()->getValue() + $basket->getPrice()->getRebate();
		$currency = $price->getCurrencyId();
		$item = $this->getServiceItem();
		$costs = 0;

		if( ( $val = $item->getConfigValue( 'reduction.basket-value-min/' . $currency ) ) !== null && $val > $total ) {
			return $price;
		}

		if( ( $val = $item->getConfigValue( 'reduction.basket-value-max/' . $currency ) ) !== null && $val < $total ) {
			return $price;
		}

		if( $item->getConfigValue( 'reduction.product-costs' ) )
		{
			foreach( $basket->getProducts() as $orderProduct )
			{
				$costs += $orderProduct->getPrice()->getCosts() * $orderProduct->getQuantity();

				foreach( $orderProduct->getProducts() as $subProduct ) {
					$costs += $subProduct->getPrice()->getCosts() * $subProduct->getQuantity();
				}
			}
		}

		$sub = ( $price->getCosts() + $costs ) * $item->getConfigValue( 'reduction.percent' ) / 100;
		return $price->setRebate( $price->getRebate() + $sub )->setCosts( $price->getCosts() - $sub );
	}
}
