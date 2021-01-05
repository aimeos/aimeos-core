<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for adding quantity based costs
 *
 * This decorator interacts with the ServiceUpdate and Autofill basket plugins!
 * If the delivery/payment option isn't available any more, the ServiceUpdate
 * plugin will remove it from the basket and the Autofill plugin will add one
 * of the available options again.
 *
 * @package MShop
 * @subpackage Service
 */
class Quantity
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'quantity.packagesize' => array(
			'code' => 'quantity.packagesize',
			'internalcode' => 'quantity.packagesize',
			'label' => 'Number of products in the package',
			'type' => 'number',
			'internaltype' => 'integer',
			'default' => '1',
			'required' => false,
		),
		'quantity.packagecosts' => array(
			'code' => 'quantity.packagecosts',
			'internalcode' => 'quantity.packagecosts',
			'label' => 'Costs per the package',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => '',
			'required' => true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 *    known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider
	 *
	 * This will generate a list of available fields and rules for the value of
	 * each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Returns the price when using the provider.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return \Aimeos\MShop\Price\Item\Iface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Price\Item\Iface
	{
		$sum = 0;
		$price = $this->getProvider()->calcPrice( $basket );

		foreach( $basket->getProducts() as $orderProduct )
		{
			$qty = $orderProduct->getQuantity();

			if( !( $products = $orderProduct->getProducts() )->isEmpty() )
			{
				foreach( $products as $prodItem ) { // calculate bundled products
					$sum += $qty * $prodItem->getQuantity();
				}
			}
			else
			{
				$sum += $qty;
			}
		}

		$size = $this->getConfigValue( array( 'quantity.packagesize' ), 1 );
		$costs = $this->getConfigValue( array( 'quantity.packagecosts' ), 0.00 );

		$value = ceil( $sum / $size ) * $costs;

		return $price->setCosts( $price->getCosts() + $value );
	}
}
