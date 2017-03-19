<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos;


/**
 * Global starting point for applicatons.
 */
class Bootstrap
{
	private $manifests = array();
	private $extensions = array();
	private $extensionsDone = array();
	private $dependencies = array();
	private static $includePaths = array();
	private static $autoloader = false;


	/**
	 * Initialises the object.
	 *
	 * @param array $extdirs List of directories to look for manifest files (or sub-directories thereof)
	 * @param boolean $defaultdir If default extension directory should be included automatically
	 * @param string|null $basedir Aimeos core path (optional, __DIR__ if null)
	 */
	public function __construct( array $extdirs = array(), $defaultdir = true, $basedir = null )
	{
		if( $basedir === null ) {
			$basedir = __DIR__;
		}

		if( $defaultdir === true && is_dir( $basedir . DIRECTORY_SEPARATOR . 'ext' ) === true ) {
			$extdirs[] = realpath( $basedir . DIRECTORY_SEPARATOR . 'ext' );
		}

		$this->manifests[$basedir] = $this->getManifestFile( $basedir );

		self::$includePaths = $this->getIncludePaths();
		$this->registerAutoloader();
		$this->addDependencies( $extdirs );
		$this->addManifests( $this->dependencies );
		self::$includePaths = $this->getIncludePaths();
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

		if( strncmp( $fileName, 'Aimeos' . DIRECTORY_SEPARATOR, 7 ) === 0 ) {
			$fileName = substr( $fileName, 7 );
		}

		foreach( self::$includePaths as $path )
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

		foreach( $this->manifests as $basePath => $manifest )
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

		foreach( $this->manifests as $path => $manifest )
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
	 * @return string[] List of configuration paths
	 */
	public function getConfigPaths()
	{
		$confpaths = array();

		foreach( $this->manifests as $path => $manifest )
		{
			if( !isset( $manifest['config'] ) ) {
				continue;
			}

			foreach( (array) $manifest['config'] as $relpath ) {
				$confpaths[] = $path . DIRECTORY_SEPARATOR . $relpath;
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

		foreach( $this->manifests as $path => $manifest )
		{
			if( isset( $manifest['custom'][$section] ) ) {
				$paths[$path] = $manifest['custom'][$section];
			}
		}

		return $paths;
	}


	/**
	 * Returns the available extensions
	 *
	 * @return array List of available extension names
	 */
	public function getExtensions()
	{
		$list = [];

		foreach( $this->manifests as $path => $manifest )
		{
			if( isset( $manifest['name'] ) && $manifest['name'] != '' ) {
				$list[] = $manifest['name'];
			} else {
				$list[] = basename( $path );
			}
		}

		return $list;
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

		foreach( $this->manifests as $path => $manifest )
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
	 * Returns the language IDs for the available translations
	 *
	 * @param string $section Section name in the i18n paths
	 * @return array List of ISO language codes
	 */
	public function getI18nList( $section )
	{
		$list = array();
		$paths = $this->getI18nPaths();
		$paths = ( isset( $paths[$section] ) ? (array) $paths[$section] : array() );

		foreach( $paths as $path )
		{
			$iter = new \DirectoryIterator( $path );

			foreach( $iter as $file )
			{
				$name = $file->getFilename();

				if( $file->isFile() && preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $name ) ) {
					$list[$name] = null;
				}
			}
		}

		ksort( $list );
		return array_keys( $list );
	}


	/**
	 * Returns the configurations of the manifest files in the given directories.
	 *
	 * @param array $directories List of directories where the manifest files are stored
	 * @return array Associative list of directory / configuration array pairs
	 */
	protected function getManifests( array $directories )
	{
		$manifests = array();

		foreach( $directories as $directory )
		{
			$manifest = $this->getManifestFile( $directory );

			if( $manifest !== false )
			{
				$manifests[$directory] = $manifest;
				continue;
			}

			$dir = new \DirectoryIterator( $directory );

			foreach( $dir as $dirinfo )
			{
				if( $dirinfo->isDot() !== false ) {
					continue;
				}

				$manifest = $this->getManifestFile( $dirinfo->getPathName() );
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
	protected function getManifestFile( $dir )
	{
		$manifestFile = $dir . DIRECTORY_SEPARATOR . 'manifest.php';

		if( is_dir( $dir ) && file_exists( $manifestFile ) ) {
			return include $manifestFile;
		}

		return false;
	}


	/**
	 * Registers the Aimeos autoloader.
	 */
	protected function registerAutoloader()
	{
		if( self::$autoloader === false )
		{
			spl_autoload_register( array( $this, 'autoload' ), true, false );
			self::$autoloader = true;
		}

		$ds = DIRECTORY_SEPARATOR;

		if( is_file( __DIR__ . $ds . 'vendor' . $ds . 'autoload.php' ) ) {
			require __DIR__ . $ds . 'vendor' . $ds . 'autoload.php';
		}
	}


	/**
	 * Adds the dependencies from the extensions
	 *
	 * @param array $extdirs List of extension directories
	 * @throws \Exception If dependencies are incorrectly configured
	 */
	private function addDependencies( array $extdirs )
	{
		foreach( $this->getManifests( $extdirs ) as $location => $manifest )
		{
			if( isset( $this->extensions[$manifest['name']] ) )
			{
				$location2 = $this->extensions[$manifest['name']]['location'];
				$msg = 'Extension "%1$s" exists twice in "%2$s" and in "%3$s"';
				throw new \Exception( sprintf( $msg, $manifest['name'], $location, $location2 ) );
			}

			if( !isset( $manifest['depends'] ) || !is_array( $manifest['depends'] ) ) {
				throw new \Exception( sprintf( 'Incorrect dependency configuration in manifest "%1$s"', $location ) );
			}

			$manifest['location'] = $location;
			$this->extensions[$manifest['name']] = $manifest;

			foreach( $manifest['depends'] as $name ) {
				$this->dependencies[$manifest['name']][$name] = $name;
			}
		}
	}


	/**
	 * Re-order the given dependencies of each manifest configuration.
	 *
	 * @param array $deps List of dependencies
	 * @param array $stack List of task names that are scheduled after this task
	 * @todo version checks
	 */
	private function addManifests( array $deps, array $stack = array( ) )
	{
		foreach( $deps as $extName => $name )
		{
			if( in_array( $extName, $this->extensionsDone ) ) {
				continue;
			}

			if( in_array( $extName, $stack ) ) {
				throw new \Exception( sprintf( 'Circular dependency for "%1$s" detected', $extName ) );
			}

			$stack[] = $extName;

			if( isset( $this->dependencies[$extName] ) ) {
				$this->addManifests( (array) $this->dependencies[$extName], $stack );
			}

			if( isset( $this->extensions[$extName] ) ) {
				$this->manifests[$this->extensions[$extName]['location']] = $this->extensions[$extName];
			}

			$this->extensionsDone[] = $extName;
		}
	}
}
