<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Abstract class for plugin provider and decorator implementations
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class MShop_Plugin_Provider_Base
{
	private $item;
	private $context;


	/**
	 * Initializes the plugin instance.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Plugin_Item_Iface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Plugin_Item_Iface $item )
	{
		$this->item = $item;
		$this->context = $context;
	}


	/**
	 * Returns the plugin item the provider is configured with.
	 *
	 * @return MShop_Plugin_Item_Iface Plugin item object
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
	 * @return MShop_Context_Item_Iface Context item object
	 */
	protected function getContext()
	{
		return $this->context;
	}
}