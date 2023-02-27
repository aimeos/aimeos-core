<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Catalog\Decorator;


/**
 * Category rule decorator.
 *
 * @package MShop
 * @subpackage Rule
 */
class Category
	extends \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Base
	implements \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Iface
{
	private array $codes;
	private array $beConfig = [
		'category.code' => [
			'code' => 'category.code',
			'internalcode' => 'category.code',
			'label' => 'Category codes',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		],
	];


	/**
	 * Initializes the rule instance
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @param \Aimeos\MShop\Rule\Provider\Iface $provider Rule provider object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Rule\Item\Iface $item,
		\Aimeos\MShop\Rule\Provider\Iface $provider )
	{
		parent::__construct( $context, $item, $provider );
		$this->codes = array_filter( explode( ',', str_replace( ' ', '', $this->getConfigValue( 'category.code', '' ) ) ) );
	}


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
		return array_merge( parent::getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	/**
	 * Applies the rule to the given products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		foreach( $product->getRefItems( 'catalog' ) as $catItem )
		{
			if( in_array( $catItem->getCode(), $this->codes ) ) {
				return $this->getProvider()->apply( $product );
			}
		}

		return false;
	}
}
