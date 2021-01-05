<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


/**
 * Base decorator methods for plugin provider.
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base
	extends \Aimeos\MShop\Plugin\Provider\Base
{
	private $provider;


	/**
	 * Initializes the plugin instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item,
		\Aimeos\MShop\Plugin\Provider\Iface $provider )
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
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$this->provider->register( $p );
		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		return $this->provider->update( $order, $action, $value );
	}


	/**
	 * Injects the outmost object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Plugin\Provider\Iface $object ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		parent::setObject( $object );

		$this->provider->setObject( $object );

		return $this;
	}


	/**
	 * Returns the next provider or decorator.
	 *
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Provider or decorator object
	 */
	protected function getProvider() : \Aimeos\MShop\Plugin\Provider\Iface
	{
		return $this->provider;
	}
}
