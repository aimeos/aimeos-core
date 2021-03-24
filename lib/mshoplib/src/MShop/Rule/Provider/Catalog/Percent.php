<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Catalog;


/**
 * Percent rule provider
 *
 * @package MShop
 * @subpackage Rule
 */
class Percent
	extends \Aimeos\MShop\Rule\Provider\Base
	implements \Aimeos\MShop\Rule\Provider\Catalog\Iface, \Aimeos\MShop\Rule\Provider\Factory\Iface
{
	private $beConfig = [
		'percent' => [
			'code' => 'percent',
			'internalcode' => 'percent',
			'label' => 'Percentage to add or subtract',
			'type' => 'number',
			'internaltype' => 'string',
			'default' => '0.00',
			'required' => true,
		],
	];


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );
		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_merge( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Applies the rule to the given product
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		$percent = $this->getConfigValue( 'percent' );

		foreach( $product->getRefItems( 'price' ) as $price )
		{
			$value = $price->getValue();
			$diff = $value * $percent / 100;
			$price->setValue( $value + $diff )->setRebate( $diff < 0 ? $diff : 0 );
		}

		return $this->isLast();
	}
}
