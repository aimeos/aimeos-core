<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
	abstract public function getConfig();


	/**
	 * Returns the configuration value for the specified path
	 *
	 * @param string $key Key of the associative array or path to value like "path/to/value"
	 * @param mixed $default Default value if no configration is found
	 * @return mixed Configuration value or array of values
	 */
	public function getConfigValue( $key, $default = null )
	{
		return $this->getArrayValue( $this->getConfig(), explode( '/', trim( $key, '/' ) ), $default );
	}


	/**
	 * Returns a configuration value from an array
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @param mixed $default Default value if no configuration is found
	 * @return mixed Found value or null if no value is available
	 */
	protected function getArrayValue( $config, $parts, $default )
	{
		if( ( $current = array_shift( $parts ) ) !== null && isset( $config[$current] ) )
		{
			if( count( $parts ) > 0 ) {
				return $this->getArrayValue( $config[$current], $parts );
			}

			return $config[$current];
		}

		return $default;
	}
}