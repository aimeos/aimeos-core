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
abstract class MShop_Plugin_Provider_Abstract
{
	private $_item;
	private $_context;


	/**
	 * Initializes the plugin instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		$this->_item = $item;
		$this->_context = $context;
	}


	/**
	 * Returns the plugin item the provider is configured with.
	 *
	 * @return MShop_Plugin_Item_Interface Plugin item object
	 */
	protected function _getItem()
	{
		return $this->_item;
	}


	/**
	 * Returns the configuration value from the service item specified by its key.
	 *
	 * @param string $key Configuration key
	 * @param mixed $default Default value if configuration key isn't available
	 * @return string|null Value from service item configuration
	 */
	protected function _getConfigValue( $key, $default = null )
	{
		$config = $this->_item->getConfig();

		if( isset( $config[$key] ) ) {
			return $config[$key];
		}

		return $default;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context item object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}
}