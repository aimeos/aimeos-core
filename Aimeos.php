<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Global starting point for applicatons.
 */
class Aimeos
{
	private $_manifests = array();
	private $_extensions = array();
	private $_extensionsDone = array();
	private $_dependencies = array();
	private static $_includePaths = array();
	private static $_autoloader = false;


	/**
	 * Initialises the object.
	 *
	 * @param array $extdirs List of directories to look for manifest files (or sub-directories thereof)
	 * @param boolean $defaultdir If default extension directory should be included automatically
	 * @param string|null $basedir Aimeos core path (optional, dirname(__FILE__) if null)
	 */
	public function __construct( array $extdirs = array(), $defaultdir = true, $basedir = null )
	{
		if( $basedir === null ) {
			$basedir = dirname( __FILE__ );
		}

		if( $defaultdir === true && is_dir( $basedir . DIRECTORY_SEPARATOR . 'ext' ) === true ) {
			$extdirs[] = $basedir . DIRECTORY_SEPARATOR . 'ext';
		}

		$this->_manifests[$basedir] = $this->_getManifestFile( $basedir );

		self::$_includePaths = $this->getIncludePaths();
		$this->_registerAutoloader();

		foreach( $this->_getManifests( $extdirs ) as $location => $manifest )
		{
			if( isset( $this->_extensions[$manifest['name']] ) )
			{
				$location2 = $this->_extensions[$manifest['name']]['location'];
				$msg = 'Extension "%1$s" exists twice in "%2$s" and in "%3$s"';
				throw new Exception( sprintf( $msg, $manifest['name'], $location, $location2 ) );
			}

			if( !isset( $manifest['depends'] ) || !is_array( $manifest['depends'] ) ) {
				throw new Exception( sprintf( 'Incorrect dependency configuration in manifest "%1$s"', $location ) );
			}

			$manifest['location'] = $location;
			$this->_extensions[$manifest['name']] = $manifest;

			foreach( $manifest['depends'] as $name ) {
				$this->_dependencies[$manifest['name']][$name] = $name;
			}
		}

		$this->_addManifests( $this->_dependencies );
		self::$_includePaths = $this->getIncludePaths();
	}


	/**
	 * Loads the class files for a given class name.
	 *
	 * @param string $className Name of the class
	 * @return boolean True if file was found, false if not
	 */
	public static function autoload( $className )
	{
		$fileName = strtr( ltrim( $className, '\\' ), '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR ) . '.php';

		foreach( self::$_includePaths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $fileName;

			if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
				return true;
			}
		}

		foreach( explode( PATH_SEPARATOR, get_include_path() ) as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $fileName;

			if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Returns the list of paths for each domain where the translation files are located.
	 *
	 * @return array Associative list of i18n domains and lists of absolute paths to the translation directories
	 */
	public function getI18nPaths()
	{
		$paths = array();

		foreach( $this->_manifests as $basePath => $manifest )
		{
			if( !isset( $manifest['i18n'] ) ) {
				continue;
			}

			foreach( $manifest['i18n'] as $domain => $location ) {
				$paths[$domain][] = $basePath . DIRECTORY_SEPARATOR . $location;
			}
		}

		return $paths;
	}


	/**
	 * Returns the include paths containing the required class files.
	 *
	 * @return array List of include paths
	 */
	public function getIncludePaths()
	{
		$includes = array();

		foreach( $this->_manifests as $path => $manifest )
		{
			if( !isset( $manifest['include'] ) ) {
				continue;
			}

			foreach( $manifest['include'] as $paths ) {
				$includes[] = $path . DIRECTORY_SEPARATOR . $paths;
			}
		}

		return $includes;
	}


	/**
	 * Returns the paths containing the required configuration files.
	 *
	 * @param string $dbtype Name of the database type, e.g. "mysql"
	 * @return string[] List of configuration paths
	 */
	public function getConfigPaths( $dbtype )
	{
		$confpaths = array();

		foreach( $this->_manifests as $path => $manifest )
		{
			if( !isset( $manifest['config'][$dbtype] ) ) {
				continue;
			}

			foreach( $manifest['config'][$dbtype] as $paths ) {
				$confpaths[] = $path . DIRECTORY_SEPARATOR . $paths;
			}
		}

		return $confpaths;
	}


	/**
	 * Returns the paths stored in the manifest file for the given custom section.
	 *
	 * @param string $section Name of the section like in the manifest file
	 * @return array List of paths
	 */
	public function getCustomPaths( $section )
	{
		$paths = array();

		foreach( $this->_manifests as $path => $manifest )
		{
			if( isset( $manifest['custom'][$section] ) ) {
				$paths[$path] = $manifest['custom'][$section];
			}
		}

		return $paths;
	}


	/**
	 * Returns the list of paths where setup tasks are stored.
	 *
	 * @param string $site Name of the site like "default", "unitperf" and "unittest"
	 * @return array List of setup paths
	 */
	public function getSetupPaths( $site )
	{
		$setupPaths = array();

		foreach( $this->_manifests as $path => $manifest )
		{
			if( !isset( $manifest['setup'] ) ) {
				continue;
			}

			foreach( $manifest['setup'] as $relpath )
			{
				$setupPaths[] = $path . DIRECTORY_SEPARATOR . $relpath;

				$sitePath = $path . DIRECTORY_SEPARATOR . $relpath . DIRECTORY_SEPARATOR . $site;

				if( is_dir( realpath( $sitePath ) ) ) {
					$setupPaths[] = $sitePath;
				}
			}
		}

		return $setupPaths;
	}


	/**
	 * Returns the configurations of the manifest files in the given directories.
	 *
	 * @param array $directories List of directories where the manifest files are stored
	 * @return array Associative list of directory / configuration array pairs
	 */
	protected function _getManifests( array $directories )
	{
		$manifests = array();

		foreach( $directories as $directory )
		{
			$manifest = $this->_getManifestFile( $directory );

			if( $manifest !== false )
			{
				$manifests[$directory] = $manifest;
				continue;
			}

			$dir = new DirectoryIterator( $directory );

			foreach( $dir as $dirinfo )
			{
				if( $dirinfo->isDot() !== false ) {
					continue;
				}

				$manifest = $this->_getManifestFile( $dirinfo->getPathName() );
				if( $manifest === false ) {
					continue;
				}

				$manifests[$dirinfo->getPathName()] = $manifest;
			}
		}

		return $manifests;
	}


	/**
	 * Loads the manifest file from the given directory.
	 *
	 * @param string $dir Directory that includes the manifest file
	 * @return array|false Associative list of configurations or false if the file doesn't exist
	 */
	protected function _getManifestFile( $dir )
	{
		$manifestFile = $dir . DIRECTORY_SEPARATOR . 'manifest.php';

		if( file_exists( $manifestFile ) ) {
			return include $manifestFile;
		}

		return false;
	}


	/**
	 * Registers the Aimeos autoloader.
	 */
	protected function _registerAutoloader()
	{
		if( self::$_autoloader === false )
		{
			spl_autoload_register( array( $this, 'autoload' ), true, false );
			self::$_autoloader = true;
		}
	}


	/**
	 * Re-order the given dependencies of each manifest configuration.
	 *
	 * @param array $deps List of dependencies
	 * @param array $stack List of task names that are scheduled after this task
	 * @todo version checks
	 */
	private function _addManifests( array $deps, array $stack = array( ) )
	{
		foreach( $deps as $extName => $name )
		{
			if( in_array( $extName, $this->_extensionsDone ) ) {
				continue;
			}

			if( in_array( $extName, $stack ) ) {
				$msg = sprintf( 'Circular dependency for "%1$s" detected', $extName );
				throw new Exception( $msg );
			}

			$stack[] = $extName;

			/** @todo test for expression object or array when implementing version checks */
			if( isset( $this->_dependencies[$extName] ) ) {
				$this->_addManifests( (array) $this->_dependencies[$extName], $stack );
			}

			if( isset( $this->_extensions[$extName] ) ) {
				$this->_manifests[$this->_extensions[$extName]['location']] = $this->_extensions[$extName];
			}

			$this->_extensionsDone[] = $extName;
		}
	}
}
