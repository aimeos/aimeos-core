<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config;


/**
 * Configuration setting class using arrays
 *
 * @package MW
 * @subpackage Config
 */
class PHPArray
	extends \Aimeos\MW\Config\Base
	implements \Aimeos\MW\Config\Iface
{
	private $config;
	private $paths;


	/**
	 * Initialize config object
	 *
	 * @param array $config Configuration array
	 * @param string|array $paths Filesystem path or list of paths to the configuration files
	 */
	public function __construct( $config = [], $paths = [] )
	{
		$this->config = $config;
		$this->paths = (array) $paths;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $name, $default = null )
	{
		$parts = explode( '/', trim( $name, '/' ) );

		if( ( $value = $this->getPart( $this->config, $parts ) ) !== null ) {
			return $value;
		}

		foreach( $this->paths as $fspath ) {
			$this->config = $this->load( $this->config, $fspath, $parts );
		}

		if( ( $value = $this->getPart( $this->config, $parts ) ) !== null ) {
			return $value;
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $name, $value )
	{
		$parts = explode( '/', trim( $name, '/' ) );
		$this->config = $this->setPart( $this->config, $parts, $value );
	}


	/**
	 * Returns a configuration value from an array.
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @return mixed Found value or null if no value is available
	 */
	protected function getPart( $config,  $parts )
	{
		if( ( $current = array_shift( $parts ) ) !== null && isset( $config[$current] ) )
		{
			if( count( $parts ) > 0 ) {
				return $this->getPart( $config[$current], $parts );
			}

			return $config[$current];
		}

		return null;
	}


	/**
	 * Sets a configuration value in the array.
	 *
	 * @param array $config Configuration sub-part
	 * @param array $path Configuration path parts
	 * @param array $value The new value
	 */
	protected function setPart( $config, $path, $value )
	{
		if( ( $current = array_shift( $path ) ) !== null )
		{
			if( isset( $config[$current] ) ) {
				$config[$current] = $this->setPart( $config[$current], $path, $value );
			} else {
				$config[$current] = $this->setPart( [], $path, $value );
			}

			return $config;
		}

		return $value;
	}


	/**
	 * Loads the configuration files when found.
	 *
	 * @param array $config Configuration array which should contain the loaded configuration
	 * @param string $path Path to the configuration directory
	 * @param array $parts List of config name parts to look for
	 * @return array Merged configuration
	 */
	protected function load( array $config, $path, array $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null )
		{
			$newPath = $path . DIRECTORY_SEPARATOR . $key;

			if( is_dir( $newPath ) )
			{
				if( !isset( $config[$key] ) ) {
					$config[$key] = [];
				}

				$config[$key] = $this->load( $config[$key], $newPath, $parts );
			}

			if( file_exists( $newPath . '.php' ) )
			{
				if( !isset( $config[$key] ) ) {
					$config[$key] = [];
				}

				$config[$key] = array_replace_recursive( $config[$key], $this->includeFile( $newPath . '.php' ) );
			}
		}

		return $config;
	}


	/**
	 * Merges a multi-dimensional array into another one
	 *
	 * @param array $left Array to be merged into
	 * @param array $right Array to merge in
	 * @deprecated Use array_replace_recursive() instead
	 */
	protected function merge( array $left, array $right )
	{
		foreach( $right as $key => $value )
		{
			if( isset( $left[$key] ) && is_array( $left[$key] ) && is_array( $value ) ) {
				$left[$key] = $this->merge( $left[$key], $value );
			} else {
				$left[$key] = $value;
			}
		}

		return $left;
	}
}