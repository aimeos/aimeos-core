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

		$ds = DIRECTORY_SEPARATOR;
		$this->_baseURL = rtrim( $baseURL, $ds ) . $ds;
		$this->_basePath = dirname( $filename ) . $ds;
		$this->_deployDir = $manifest->deployDir . $ds;

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
		$filesToDisplay = array();

		foreach ( $this->_registeredPackages as $filetype => $packageList )
		{
			if( $type !== null && $filetype !== $type ) {
				continue;
			}

			foreach( $packageList as $package )
			{
				$usePackage = true;
				$packageFile = $this->_deployDir . $package->file;
				$packageFileURL = $this->_baseURL . $packageFile;
				$packageFileFilesystem = $this->_basePath . $packageFile;
				$packageFileTime = 0;

				if( is_file( $packageFileFilesystem ) ) {
					$packageFileTime = filemtime( $packageFileFilesystem );
				}

				$filesToDisplay = array();

				foreach( $package->fileIncludes as $singleFile )
				{
					$filename = $this->_basePath . $singleFile->path . $singleFile->text;

					if( !is_file( $filename ) || ( $fileTime = filemtime( $filename ) ) === false ) {
						throw new MW_Jsb2_Exception( sprintf( 'Unable to read filetime of file "%1$s"', $filename ) );
					}

					if( !$packageFileTime || $fileTime > $packageFileTime ) {
						$usePackage = false;
					}

					$filesToDisplay[] = $this->_baseURL . $singleFile->path . $singleFile->text . '?v=' . $fileTime;
				}

				if( $usePackage === true) {
					$filesToDisplay = array( $packageFileURL . '?v=' . $packageFileTime );
				}

				foreach( $filesToDisplay as $singleFile )
				{
					switch( $filetype )
					{
						case 'js':
							$html .= '<script type="text/javascript" src="' . $singleFile . '"></script>' . PHP_EOL;
							break;
						case 'css':
							$html .= '<link rel="stylesheet" type="text/css" href="' . $singleFile . '"/>' . PHP_EOL;
							break;
						default:
							throw new MW_Jsb2_Exception( sprintf( 'Unknown file extension: "%1$s"', $filetype ) );
					}
				}
			}
		}

		return $html;
	}


	/**
	 * Creates minified packages files.
	 *
	 * @param string $type Specific filetypes to create output
	 * @param boolean $debug If true no compression is applied to the files
	 * @param octal $filepermission Set permissions for created package files
	 * @param octal $dirpermission Set permissions for created directorys
	 */
	public function deploy( $type = null, $debug = true, $filepermission = 0644, $dirpermission = 0755 )
	{
		$ds = DIRECTORY_SEPARATOR;

		foreach( $this->_registeredPackages as $filetype => $packageFiles )
		{
			if( $type !== null && $filetype !== $type ) {
				continue;
			}

			foreach( $packageFiles as $package)
			{
				$packageFile = $this->_basePath . $this->_deployDir . $package->file;

				$packageDir = dirname( $packageFile );

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
	 * Creates minified file for given package using JSMin.
	 *
	 * @param object $package Package object from manifest to minify
	 * @param boolean $debug Create debug files if true
	 * @param octal $permissions File permissions to set on new files
	 */
	protected function _minify( $package, $debug, $permissions )
	{
		$content = '';
		$ds = DIRECTORY_SEPARATOR;

		foreach( $this->_getFilenames( $package, $this->_basePath ) as $filename )
		{
			if( ( $content .= file_get_contents( $filename ) ) === false ) {
				throw new MW_Jsb2_Exception( sprintf( 'Unable to get content of file "%1$s"', $filename ) );
			}
		}

		if( $debug !== true ) {
			$content = JSMin::minify( $content );
		}

		$pkgFileName = $this->_basePath . $this->_deployDir . $package->file;

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

		foreach( $package->fileIncludes as $include )
		{
			if( !is_object( $include ) ) {
				throw new MW_Jsb2_Exception( 'Invalid file inlcude' );
			}

			$filename = $include->path . $include->text;

			if( !file_exists( $this->_basePath . $filename ) ) {
				throw new MW_Jsb2_Exception( sprintf( 'File does not exists: "%1$s"', $filename ) );
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