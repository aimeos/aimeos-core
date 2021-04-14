<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Config;


/**
 * Common trait for items containing configurations
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	abstract public function getConfig() : array;


	/**
	 * Sets the configuration values of the item
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	abstract public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface;


	/**
	 * Returns the configuration value for the specified path
	 *
	 * @param string $key Key of the associative array or path to value like "path/to/value"
	 * @param mixed $default Default value if no configration is found
	 * @return mixed Configuration value or array of values
	 */
	public function getConfigValue( string $key, $default = null )
	{
		return $this->getArrayValue( $this->getConfig(), explode( '/', trim( $key, '/' ) ), $default );
	}


	/**
	 * Sets the configuration value for the specified path
	 *
	 *  Setting "value" by using "path/to" as key would result in:
	 *  [
	 *    'path' => [
	 *      'to' => 'value'
	 *    ]
	 *  ]
	 *
	 * @param string $key Key of the associative array or path to value like "path/to/value"
	 * @param mixed $value Value to set for the key
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setConfigValue( string $key, $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->setConfig( $this->setArrayValue( $this->getConfig(), explode( '/', trim( $key, '/' ) ), $value ) );
	}


	/**
	 * Returns a configuration value from an array
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @param mixed $default Default value if no configuration is found
	 * @return mixed Found value or null if no value is available
	 */
	protected function getArrayValue( array $config, array $parts, $default )
	{
		if( ( $current = array_shift( $parts ) ) !== null && isset( $config[$current] ) )
		{
			if( count( $parts ) > 0 )
			{
				if( is_array( $config[$current] ) ) {
					return $this->getArrayValue( $config[$current], $parts, $default );
				}

				return $default;
			}

			return $config[$current];
		}

		return $default;
	}


	/**
	 * Sets the value for the given key parts in the array configuration
	 *
	 * @param array $config The configuration array to set the key/value pair in
	 * @param array $parts Configuration path parts to use in the array
	 * @param mixed $value Value to set in the configuration array
	 * @return array Modified configuration array
	 */
	protected function setArrayValue( array $config, array $parts, $value ) : array
	{
		$current = array_shift( $parts );

		if( !empty( $parts ) ) {
			$config[$current] = $this->setArrayValue( $config[$current] ?? [], $parts, $value );
		} else {
			$config[$current] = $value;
		}

		return $config;
	}
}
