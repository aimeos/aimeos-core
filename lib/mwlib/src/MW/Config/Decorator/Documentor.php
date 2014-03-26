<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 */


/**
 * Documentor decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Decorator_Documentor
	extends MW_Config_Decorator_Abstract
	implements MW_Config_Decorator_Interface
{
	private $_file;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 * @param string $filename File name the collected configuration is written to
	 */
	public function __construct( MW_Config_Interface $object, $filename = 'confdoc.ser' )
	{
		parent::__construct( $object );

		// this object is not cloned!
		$this->_file = new My_Config_File( $filename );
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
		$value = $this->_getObject()->get( $name, $default );

		$this->_file->set( $name, $value, $default );

		return $value;
	}
}


class My_Config_File
{
	private $_config = array();
	private $_file;


	public function __construct( $filename )
	{
		if( ( $this->_file = fopen( $filename, 'w' ) ) === false ) {
			throw new MW_Config_Exception( sprintf( 'Unable to open file "%1$s"', $filename ) );
		}
	}


	public function __destruct()
	{
		if( fwrite( $this->_file, serialize( $this->_config ) ) === false ) {
			echo 'Unable to write collected configuration to file' . PHP_EOL;
		}

		fclose( $this->_file );
	}


	public function set( $name, $value, $default )
	{
		$this->_config[$name]['value'] = $value;
		$this->_config[$name]['default'] = $default;
	}
}
