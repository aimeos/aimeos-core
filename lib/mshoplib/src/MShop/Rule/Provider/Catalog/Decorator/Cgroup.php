<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Catalog\Decorator;


/**
 * Customer group rule decorator.
 *
 * @package MShop
 * @subpackage Rule
 */
class Cgroup
	extends \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Base
	implements \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Iface
{
	private $result;
	private $beConfig = [
		'cgroup.map' => [
			'code' => 'cgroup.map',
			'internalcode' => 'cgroup.map',
			'label' => 'Customer group ID/name pairs',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => true,
		],
	];


	/**
	 * Initializes the rule instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Rule\Item\Iface $item Rule item object
	 * @param \Aimeos\MShop\Rule\Provider\Iface $provider Rule provider object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Rule\Item\Iface $item,
		\Aimeos\MShop\Rule\Provider\Iface $provider )
	{
		parent::__construct( $context, $item, $provider );

		$ids = array_keys( $this->getConfigValue( 'cgroup.id', [] ) );
		$this->result = array_intersect( $ids, $context->getGroupIds() ) !== [];
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
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Applies the rule to the given products
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		return $this->result ? $this->getProvider()->apply( $product ) : false;
	}
}
