<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider;


/**
 * Abstract class for plugin provider and decorator implementations
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base
{
	private $item;
	private $context;
	private $object;


	/**
	 * Initializes the plugin instance.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item )
	{
		$this->item = $item;
		$this->context = $context;
	}


	/**
	 * Injects the outer object into the decorator stack
	 *
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $object First object of the decorator stack
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Plugin\Provider\Iface $object )
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Returns the first object of the decorator stack
	 *
	 * @return \Aimeos\MShop\Plugin\Provider\Iface First object of the decorator stack
	 */
	protected function getObject()
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Returns the plugin item the provider is configured with.
	 *
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item object
	 */
	protected function getItemBase()
	{
		return $this->item;
	}


	/**
	 * Returns the configuration value from the service item specified by its key.
	 *
	 * @param string $key Configuration key
	 * @param mixed $default Default value if configuration key isn't available
	 * @return string|null Value from service item configuration
	 */
	protected function getConfigValue( $key, $default = null )
	{
		$config = $this->item->getConfig();

		if( isset( $config[$key] ) ) {
			return $config[$key];
		}

		return $default;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item object
	 */
	protected function getContext()
	{
		return $this->context;
	}
}