<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Rule
 */


namespace Aimeos\MShop\Rule\Provider\Catalog\Decorator;


/**
 * Base decorator methods for rule provider.
 *
 * @package MShop
 * @subpackage Rule
 */
abstract class Base
	extends \Aimeos\MShop\Rule\Provider\Base
	implements Iface
{
	private $provider;


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
		parent::__construct( $context, $item );

		$this->provider = $provider;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->provider->checkConfigBE( $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->provider->getConfigBE();
	}


	/**
	 * Applies the rule to the given product
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product the rule should be applied to
	 * @return bool True if rule is the last one, false to continue with further rules
	 */
	public function apply( \Aimeos\MShop\Product\Item\Iface $product ) : bool
	{
		return $this->provider->apply( $product );
	}


	/**
	 * Injects the outmost object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Rule\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Rule\Provider\Iface Rule object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Rule\Provider\Iface $object ) : \Aimeos\MShop\Rule\Provider\Iface
	{
		parent::setObject( $object );

		$this->provider->setObject( $object );

		return $this;
	}


	/**
	 * Returns the next provider or decorator.
	 *
	 * @return \Aimeos\MShop\Rule\Provider\Iface Provider or decorator object
	 */
	protected function getProvider() : \Aimeos\MShop\Rule\Provider\Iface
	{
		return $this->provider;
	}
}
