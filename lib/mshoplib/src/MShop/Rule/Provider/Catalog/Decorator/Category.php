<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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
	private $beConfig = [
		'category.code' => [
			'code' => 'category.code',
			'internalcode' => 'category.code',
			'label' => 'Category codes',
			'type' => 'list',
			'internaltype' => 'array',
			'default' => [],
			'required' => true,
		],
	];


	/**
	 * Applies the rule to the given products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		$codes = $this->getConfigValue( 'category.code', [] );

		foreach( $product->getCatalogItems() as $catItem )
		{
			if( in_array( $catItem->getCode(), $codes ) ) {
				return $this->getProvider()->apply( $product );
			}
		}

		return false;
	}
}
