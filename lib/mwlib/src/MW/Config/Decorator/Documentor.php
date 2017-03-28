<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config\Decorator;


/**
 * Documentor decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class Documentor
	extends \Aimeos\MW\Config\Decorator\Base
	implements \Aimeos\MW\Config\Decorator\Iface
{
	private $file;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 * @param string $filename File name the collected configuration is written to
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object, $filename = 'confdoc.ser' )
	{
		parent::__construct( $object );

		// this object is not cloned!
		$this->file = new ConfigFile( $filename );
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
		$value = parent::get( $name, $default );

		$this->file->set( $name, $value, $default );

		return $value;
	}
}


/**
 * File writer for the documentor decorator config classe.
 *
 * @package MW
 * @subpackage Config
 */
class ConfigFile
{
	private $config = [];
	private $file;


	/**
	 * Initializes the instance.
	 *
	 * @param string $filename
	 * @throws \Aimeos\MW\Config\Exception If file could not be opened or created
	 */
	public function __construct( $filename )
	{
		if( ( $this->file = fopen( $filename, 'w' ) ) === false ) {
			throw new \Aimeos\MW\Config\Exception( sprintf( 'Unable to open file "%1$s"', $filename ) );
		}
	}


	/**
	 * Cleans up when the object is destroyed.
	 */
	public function __destruct()
	{
		if( fwrite( $this->file, serialize( $this->config ) ) === false ) {
			echo 'Unable to write collected configuration to file' . PHP_EOL;
		}

		fclose( $this->file );
	}


	/**
	 * Stores the configuration key, the actual and the default value
	 *
	 * @param string $name Configuration key
	 * @param string $value Configuration value
	 * @param string $default Default value
	 */
	public function set( $name, $value, $default )
	{
		$this->config[$name]['value'] = $value;
		$this->config[$name]['default'] = $default;
	}
}
