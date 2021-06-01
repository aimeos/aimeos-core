<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
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


	/**
	 * Initialize config object
	 *
	 * @param array $config Configuration array
	 * @param string|array $paths Filesystem path or list of paths to the configuration files
	 */
	public function __construct( array $config = [], $paths = [] )
	{
		$this->config = $config;

		foreach( (array) $paths as $fspath ) {
			$this->config = $this->load( $this->config, $fspath );
		}
	}


	/**
	 * Adds the given configuration and overwrite already existing keys.
	 *
	 * @param array $config Associative list of (multi-dimensional) configuration settings
	 * @return \Aimeos\MW\Config\Iface Config instance for method chaining
	 */
	public function apply( array $config ) : Iface
	{
		$this->config = array_replace_recursive( $this->config, $config );
		return $this;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null )
	{
		$parts = explode( '/', trim( $name, '/' ) );

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
	 * @return \Aimeos\MW\Config\Iface Config instance for method chaining
	 */
	public function set( string $name, $value ) : Iface
	{
		$this->config = $this->setPart( $this->config, explode( '/', trim( $name, '/' ) ), $value );
		return $this;
	}


	/**
	 * Returns a configuration value from an array.
	 *
	 * @param array $config The array to search in
	 * @param string[] $parts Configuration path parts to look for inside the array
	 * @return mixed Found value or null if no value is available
	 */
	protected function getPart( array $config, array $parts )
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
	 * @param mixed $config Configuration sub-part
	 * @param string[] $path Configuration path parts
	 * @param mixed $value The new value
	 */
	protected function setPart( $config, array $path, $value )
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
	 * @return array Merged configuration
	 */
	protected function load( array $config, string $path ) : array
	{
		if( is_dir( $path ) )
		{
			foreach( new \DirectoryIterator( $path ) as $entry )
			{
				if( $entry->isDot() ) {
					continue;
				}

				$key = $entry->getBasename( '.php' );
				$filepath = $entry->getPathname();

				if( !isset( $config[$key] ) ) {
					$config[$key] = [];
				}

				if( $entry->isDir() ) {
					$config[$key] = $this->load( $config[$key], $filepath );
				} elseif( $entry->isFile() ) {
					$config[$key] = array_replace_recursive( $config[$key], $this->includeFile( $filepath ) );
				}
			}
		}

		return $config;
	}
}
