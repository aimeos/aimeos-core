<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id$
 */


/**
 * Configuration setting class using arrays
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Array extends MW_Config_Abstract implements MW_Config_Interface
{
	protected $_config = array();
	protected $_cache = array();
	protected $_negCache = array();
	protected $_paths = array();


	public function __construct( $config = array(), $paths = array() )
	{
		if( $config instanceof Zend_Config ){
			$config = $config->toArray();
		}

		if( !is_array( $config ) ) {
			throw new Exception( 'First argument must be an array.' );
		}

		$this->_config = $config;

		foreach( (array)$paths as $path )
		{
			if( file_exists( $path ) ) {
				$this->_paths[] = $path;
			}
		}
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

		if( isset( $this->_negCache[ $name ] ) && ( $this->_negCache[ $name ] === true ) ) {
			return $default;
		}

		if( isset( $this->_cache[ $name ] ) ) {
			return $this->_cache[ $name ];
		}

		$path = explode( '/', $name );

		$return = $this->_getFromArray( $path, $this->_config );

		if( $return === null )
		{
			$filePaths = $this->_findFile( $path );

			$subConfig = array();
			foreach( $filePaths as $filePath )
			{
				$add = $this->_include( $filePath['file'] );

				if( is_array( $add ) ) {
					$this->_merge( $subConfig, $this->_makeMap( $filePath['prefix'], $add ) );
				}
			}

			$return = $this->_getFromArray( $path, $subConfig );
		}

		if( $return === null || $return === array() )
		{
			$this->_negCache[ $name ] = true;
			return $default;
		}

		$this->_cache[ $name ] = $return;
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
		if( $value === null ) {
			$this->_negCache[ $name ] = true;
		} else {
			$name = trim( $name, '/' );
			$this->_cache[ $name ] = $value;

			if( isset( $this->_negCache[ $name ] ) ) {
				unset( $this->_negCache[ $name ] );
			}
		}
	}


	/**
	 * Creates a configuration array that can be merged into $_config
	 *
	 * @param array $keys path from configuration root to the new configuration part
	 * @param array $inner new configuration part
	 * @return array with all keys matching the $_config
	 */
	protected function _makeMap( $keys, $inner )
	{
		$map = array();
		$key = array_shift( $keys );

		if( !empty( $keys ) ) {
			$map[ $key ] = $this->_makeMap( $keys, $inner );
		} else {
			$map[ $key ] = $inner;
		}

		return $map;
	}


	/**
	 * Merges a multi-dimensional array into another one
	 *
	 * @param array $left Array to be merged into
	 * @param array $right Array to merge in
	 */
 	protected function _merge( array &$left, array $right )
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
						$this->_merge( $lvalue, $rvalue );
					} else {
						$lvalue = $rvalue;
					}
				}
			}
			$left[ $lkey ] = $lvalue;
		}

		if( $match === false ) {
			$left = array_merge( $left, $right );
		}
	}


	/**
	 * Gets a configuration value from an array
	 *
	 * @param Array $path Configuration path to look for inside the array
	 * @param Array $config The array to search in
	 */
	protected function _getFromArray( $path, $config )
	{
		$current = array_shift( $path );

		if( isset( $config[ $current ] ) )
		{
			if( count( $path ) > 0 ) {
				return $this->_getFromArray( $path, $config[ $current ] );
			}
			return $config[ $current ];
		}
		return null;
	}

	
	/**
	 * Finds files within a configuration path
	 *
	 * @param array $path configuration path
	 * @return array of pairs of file paths and prefixes
	 */
	protected function _findFile( array $path )
	{
		if( isset( $this->_fileCache[ implode( $path, '/' ) ] ) ) {
			return $this->_fileCache[ implode( $path, '/' ) ];
		}

		$ds = DIRECTORY_SEPARATOR;

		$return = array();

		foreach( $this->_paths as $configPath )
		{
			$dirs = '';
			$prefix = array();

			$found = false;

			foreach( $path as $dir )
			{
				$dirs .= $ds . $dir;
				$currentPath = $configPath . $dirs . '.php';
				$prefix[] = $dir;

				if( file_exists( $currentPath ) )
				{
					$return[] = array( 'file' => $currentPath, 'prefix' => $prefix );
					$found = true;
					continue;
				}
			}

			if( $found === false && file_exists( $configPath . $ds . implode( $path, $ds ) ) )
			{
				$folder = $configPath . $ds . implode( $path, $ds );
				$this->_getAllFiles( $folder, $path, $return );
			}
		}

		$this->_fileCache[ implode( $path, '/' ) ] = $return;
		return $return;
	}


	/**
	 * Finds all .php files in a folder, including sub-folders
	 *
	 * @param String $path Path in the file system
	 * @param array $prefix list of prefix elements
	 * @param &array $return array with pairs of files and prefixes
	 */
	protected function _getAllFiles( $path, $prefix, &$return )
	{
		$dir = opendir( $path );
		$content = array();
		while( $entry = readdir( $dir ) )
		{
			if( substr( $entry, 0, 1 ) !== '.' ) {
				$content[] = $entry;
			}
		}
		closedir( $dir );

		foreach( $content as $entry )
		{
			if( is_dir( $entry ) )
			{
				$this->_getAllFiles( $path . DIRECTORY_SEPARATOR . $entry, $return );

			} else {

				if( substr( $entry, -4, 4 ) == '.php' )
				{
					$prefix[] = substr( $entry, 0, -4 );
					$return[] = array( 'file' => $path . DIRECTORY_SEPARATOR . $entry, 'prefix' => $prefix );
					continue;
				}
			}
		}
	}
}