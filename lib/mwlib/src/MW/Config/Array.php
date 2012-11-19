<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id$
 */


/**
 * Configuration setting class using arrays
 *
 * @author jevers
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Array implements MW_Config_Interface
{
	private $_config = array();
	private $_cache = array();
	private $_paths = array();


	public function __construct( $config = array(), $paths = array() )
	{
		$this->_config = $config;
		$this->_paths = (array) $paths;
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
		$name = trim( $name, '/' );

		if( array_key_exists( $name, $this->_cache ) ) {
			return $this->_cache[ $name ];
		}

		$path = explode( '/', $name );

		$return = $this->_get( $path, $this->_config );

		if( $return === null )
		{
			$filePaths = $this->_findFile( $path );

			foreach( $filePaths as $filePath ) {
				$this->_addFileToConfig( $filePath['prefix'], $filePath['file'] );
			}

			$return = $this->_get( $path, $this->_config );
		}

		if( $return === null ) {
			return $default;
		}

		return $return;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $name, $value )
	{
		$name = trim( $name, '/' );
		$this->_cache[ $name ] = $value;
	}


	protected function _makeMap( $keys, $inner )
	{
		$r = array();
		$key = array_shift( $keys );

		if( !empty( $keys ) ) {
			$r[ $key ] = $this->_makeMap( $keys, $inner );
		} else {
			$r[ $key ] = $inner;
		}

		return $r;
	}


	protected function _addFileToConfig( $keys, $path )
	{
		$input = include $path;

		$map = $this->_makeMap( $keys, $input );

		if( is_array( $input ) ) {
			$this->_config = $this->_merge( $this->_config, $map );
		}
	}


	protected function _findFile( array $path )
	{
		$ds = DIRECTORY_SEPARATOR;

		$return = array();

		foreach( $this->_paths as $configPath )
		{
			$dirs = '';
			$prefix = array();

			foreach( $path as $dir )
			{
				$dirs .= $ds . $dir;
				$currentPath = $configPath . $dirs . '.php';
				$prefix[] = $dir;

				if( file_exists( $currentPath ) )
				{
					$return[] = array( 'file' => $currentPath, 'prefix' => $prefix );
					continue;
				}
			}
		}
		return $return;
	}


	/**
	 *
	 */
	protected function _merge( array $left, array $right )
	{
		$match = false;
		foreach( $left as $lkey => $lvalue )
		{
			foreach( $right as $rkey => $rvalue )
			{
				if( $lkey == $rkey )
				{
					$match = true;
					if( is_array( $lvalue ) && is_array( $rvalue ) ) {
						$lvalue = $this->_merge( $lvalue, $rvalue );
					} else {
						$lvalue = $rvalue;
					}
					$left[ $lkey ] = $lvalue;
				}
			}
		}

		if( $match === false ) {
			$left = array_merge( $left, $right );
		}

		return $left;
	}


	/**
	 * Gets a configuration value from an array
	 *
	 * @param String $path Configuration path to look for inside the array
	 * @param Array $config The array to search in
	 */
	protected function _get( $path, $config )
	{
		$current = array_shift( $path );

		if( array_key_exists( $current, $config ) )
		{
			if( count( $path ) > 0 ) {
				return $this->_get( $path, $config[ $current ] );
			}
			return $config[ $current ];
		}
		return null;
	}

}