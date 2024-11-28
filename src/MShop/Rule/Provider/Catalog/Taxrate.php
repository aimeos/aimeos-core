<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Catalog;


/**
 * Tax rate rule provider
 *
 * @package MShop
 * @subpackage Rule
 */
class Taxrate
	extends \Aimeos\MShop\Rule\Provider\Base
	implements \Aimeos\MShop\Rule\Provider\Catalog\Iface, \Aimeos\MShop\Rule\Provider\Factory\Iface
{
	private array $beConfig = [
		'taxrate' => [
			'code' => 'taxrate',
			'internalcode' => 'taxrate',
			'label' => 'Product tax rate',
			'type' => 'number',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return array_replace( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Applies the rule to the given product
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		$taxrate = $this->getConfigValue( 'taxrate' );

		foreach( $product->getRefItems( 'product', null, 'default' ) as $subproduct ) {
			$subproduct->getRefItems( 'price' )->setTaxrate( $taxrate );
		}

		$product->getRefItems( 'price' )->setTaxrate( $taxrate );
		return $this->isLast();
	}
}
