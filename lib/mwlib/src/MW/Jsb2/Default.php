<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Jsb2
 */


/**
 *
 * Generates compressed JS files read from a .jsb2 package.
 *
 * @package MW
 * @subpackage Jsb2
 */
class MW_Jsb2_Default
{
	private $_registeredPackages = array();
	private $_baseURL = '';
	private $_basePath = '';
	private $_deployDir = '';


	/**
	 * Initializes the Jsb2 object.
	 *
	 * @param string $filename Path to manifest file
	 * @param string_type $baseURL Base URL for HTML output
	 * @param array $filter Which packages  schould NOT be returned
	 */
	public function __construct( $filename, $baseURL = "", $filter = array() )
	{
		$manifest = $this->_getManifest( $filename );

		$this->_baseURL = rtrim( $baseURL, '/' ) . '/';
		$this->_basePath = dirname( $filename ) . '/';
		$this->_deployDir = $manifest->deployDir . '/';

		$this->_registeredPackages = $this->_getPackages( $manifest, $filter );
	}


	/**
	 * Returns HTML for packages files with given filter.
	 *
	 * @param string $type Specific filetypes to create output
	 */
	public function getHTML( $type = null )
	{
		$html = '';
		$param = '?v=';

		if( strpos( $this->_baseURL, '?' ) !== false ) {
			$param = '&v=';
		}

		foreach ( $this->_registeredPackages as $filetype => $packageList )
		{
			if( $type !== null && $filetype !== $type ) {
				continue;
			}

			foreach( $packageList as $package )
			{
				$usePackage = true;
				$packageFile = $this->_deployDir . $package->file;
				$packageFileFilesystem = $this->_basePath . $packageFile;
				$packageFileTime = 0;
				$timestamp = 0;

				if( DIRECTORY_SEPARATOR !== '/' ) {
					$packageFileFilesystem = str_replace( '/', DIRECTORY_SEPARATOR, $packageFileFilesystem );
				}

				if( is_file( $packageFileFilesystem ) ) {
					$packageFileTime = filemtime( $packageFileFilesystem );
				}

				$filesToDisplay = $this->_getFileUrls( $this->_baseURL, $this->_basePath, $param, $package, $timestamp );

				if( $packageFileTime >= $timestamp ) {
					$filesToDisplay = array( $this->_baseURL . $packageFile . $param . $packageFileTime );
				}

				$html .= $this->_createHtml( $filesToDisplay, $filetype );
			}
		}

		return $html;
	}


	/**
	 * Creates minified packages files.
	 *
	 * @param string $type Specific filetypes to create output
	 * @param boolean $debug If true no compression is applied to the files
	 * @param integer $filepermission Set permissions for created package files
	 * @param integer $dirpermission Set permissions for created directorys
	 */
	public function deploy( $type = null, $debug = true, $filepermission = 0644, $dirpermission = 0755 )
	{
		foreach( $this->_registeredPackages as $filetype => $packageFiles )
		{
			if( $type !== null && $filetype !== $type ) {
				continue;
			}

			foreach( $packageFiles as $package)
			{
				$packageFile = $this->_basePath . $this->_deployDir . $package->file;

				$packageDir = dirname( $packageFile );

				if( DIRECTORY_SEPARATOR !== '/' ) {
					$packageDir = str_replace( '/', DIRECTORY_SEPARATOR, $packageDir );
				}

				if( !is_dir( $packageDir ) )
				{
					if( mkdir( $packageDir, $dirpermission, true ) === false ) {
						throw new MW_Jsb2_Exception( sprintf( 'Unable to create path for package file "%1$s"', $packageDir ) );
					}
				}

				$this->_minify( $package, $debug, $filepermission );
			}
		}
	}


	/**
	 * Generates the tags for the HTML head.
	 *
	 * @param array $files List of file URLs that should be HTML tags generated for
	 * @param string $filetype Typ of the given files, e.g. 'js' or 'css'
	 * @return string Generated string for inclusion into the HTML head
	 * @throws MW_Jsb2_Exception If the file type is unknown
	 */
	protected function _createHtml( array $files, $filetype )
	{
		$html = '';

		foreach( $files as $file )
		{
			switch( $filetype )
			{
				case 'js':
					$html .= '<script type="text/javascript" src="' . $file . '"></script>' . PHP_EOL;
					break;
				case 'css':
					$html .= '<link rel="stylesheet" type="text/css" href="' . $file . '"/>' . PHP_EOL;
					break;
				default:
					throw new MW_Jsb2_Exception( sprintf( 'Unknown file extension: "%1$s"', $filetype ) );
			}
		}

		return $html;
	}


	/**
	 * Returns the file URLs of the given package object.
	 *
	 * @param string $baseUrl URL the file location is relative to
	 * @param string $basePath Absolute path to the base directory of the files
	 * @param string $param Name and separator for the modification time parameter
	 * @param stdClass $package Object with "fileIncludes" property containing a
	 * 	list of file objects with "path" and "text" properties
	 * @param integer &$timestamp Value/result parameter that will contain the latest file modification timestamp
	 * @throws MW_Jsb2_Exception If the file modification timestamp couldn't be determined
	 */
	protected function _getFileUrls( $baseUrl, $basePath, $param, stdClass $package, &$timestamp )
	{
		$timestamp = (int) $timestamp;
		$filesToDisplay = array();

		foreach( $package->fileIncludes as $singleFile )
		{
			$filename = $basePath . $singleFile->path . $singleFile->text;

			if( DIRECTORY_SEPARATOR !== '/' ) {
				$filename = str_replace( '/', DIRECTORY_SEPARATOR, $filename );
			}

			if( !is_file( $filename ) || ( $fileTime = filemtime( $filename ) ) === false ) {
				throw new MW_Jsb2_Exception( sprintf( 'Unable to read filetime of file "%1$s"', $filename ) );
			}

			$timestamp = max( $timestamp, $fileTime );
			$filesToDisplay[] = $baseUrl . $singleFile->path . $singleFile->text . $param . $fileTime;
		}

		return $filesToDisplay;
	}


	/**
	 * Creates minified file for given package using JSMin.
	 *
	 * @param object $package Package object from manifest to minify
	 * @param boolean $debug Create debug files if true
	 * @param integer $permissions File permissions to set on new files
	 */
	protected function _minify( $package, $debug, $permissions )
	{
		$content = '';
		$ds = DIRECTORY_SEPARATOR;

		foreach( $this->_getFilenames( $package, $this->_basePath ) as $filename )
		{
			if( $ds !== '/' ) {
				$filename = str_replace( '/', $ds, $filename );
			}

			if( ( $content .= file_get_contents( $filename ) ) === false ) {
				throw new MW_Jsb2_Exception( sprintf( 'Unable to get content of file "%1$s"', $filename ) );
			}
		}

		if( $debug !== true ) {
			$content = JSMin::minify( $content );
		}

		$pkgFileName = $this->_basePath . $this->_deployDir . $package->file;

		if( $ds !== '/' ) {
			$pkgFileName = str_replace( '/', $ds, $pkgFileName );
		}

		if( file_put_contents( $pkgFileName, $content ) === false ) {
			throw new MW_Jsb2_Exception( sprintf( 'Unable to create package file "%1$s"', $pkgFileName ) );
		}

		if( chmod( $pkgFileName, $permissions ) === false ) {
			throw new MW_Jsb2_Exception( sprintf( 'Unable to change permissions of file "%1$s"', $pkgFileName ) );
		}
	}


	/**
	 * Get the packages from a JSON decoded manifest and validates them.
	 *
	 * @param object JSON decoded manifest
	 * @param array $filter What packages should NOT be returned
	 */
	protected function _getPackages( $manifest, $filter = array() )
	{
		$filenames = array();
		$packageContainer = array();

		if( !isset( $manifest->pkgs ) || !is_array( $manifest->pkgs ) ) {
			throw new MW_Jsb2_Exception( 'No packages found' );
		}

		foreach( $manifest->pkgs as $package )
		{
			if( !isset( $package->name ) || !isset( $package->file ) || !is_object( $package ) ) {
				throw new MW_Jsb2_Exception( 'Invalid package content' );
			}

			if( !isset( $package->fileIncludes ) || !is_array( $package->fileIncludes ) ) {
				throw new MW_Jsb2_Exception( 'No files in package found' );
			}

			if( !in_array( $package->name, $filter ) ) {
				$packageContainer[ pathinfo( $package->file, PATHINFO_EXTENSION ) ][] = $package;
			}
		}

		return $packageContainer;
	}


	/**
	 * Gets files stored in package an checkes for existence.
	 *
	 * @param object $package Single package from manifest
	 * @param string $prePath String added before filepaths
	 */
	protected function _getFilenames( $package, $prePath = '' )
	{
		$filenames = array();
		$ds = DIRECTORY_SEPARATOR;

		foreach( $package->fileIncludes as $include )
		{
			if( !is_object( $include ) ) {
				throw new MW_Jsb2_Exception( 'Invalid file inlcude' );
			}

			$filename = $include->path . $include->text;
			$absfilename = $this->_basePath . $filename;

			if( $ds !== '/' ) {
				$absfilename = str_replace( '/', $ds, $absfilename );
			}

			if( !file_exists( $absfilename ) ) {
				throw new MW_Jsb2_Exception( sprintf( 'File does not exists: "%1$s"', $absfilename ) );
			}

			$filenames[] = $prePath . $filename;
		}

		return $filenames;
	}


	/**
	 * Returns the content of a manifest file.
	 *
	 * @param string $filepath Path to manifest
	 * @throws MW_Jsb2_Exception
	 */
	protected function _getManifest( $filepath )
	{
		if( !file_exists( $filepath ) ) {
			throw new MW_Jsb2_Exception( sprintf( 'File does not exists: "%1$s"', $filepath ) );
		}

		if( ( $content = file_get_contents( $filepath ) ) === false ) {
			throw new MW_Jsb2_Exception( sprintf( 'Unable to read content from "%1$s"', $filepath ) );
		}

		if( ( $content = json_decode( $content ) ) === null ) {
			throw new MW_Jsb2_Exception( 'File content is not JSON encoded' );
		}

		return $content;
	}
}