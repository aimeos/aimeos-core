<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Jsb2
 */


namespace Aimeos\MW\Jsb2;


/**
 *
 * Generates compressed JS files read from a .jsb2 package.
 *
 * @package MW
 * @subpackage Jsb2
 */
class Standard
{
	private $registeredPackages = [];
	private $baseURL = '';
	private $basePath = '';


	/**
	 * Initializes the Jsb2 object.
	 *
	 * @param string $filename Path to manifest file
	 * @param string $baseURL Base URL for HTML output
	 * @param string[] $filter Which packages should NOT be returned
	 */
	public function __construct( string $filename, string $baseURL = '', array $filter = [] )
	{
		$manifest = $this->getManifest( $filename );

		$this->baseURL = rtrim( $baseURL, '/' ) . '/';
		$this->basePath = dirname( $filename ) . '/';

		$this->registeredPackages = $this->getPackages( $manifest, $filter );
	}


	/**
	 * Returns the list of URLs for packages files with given filter.
	 *
	 * @param string $type Specific filetypes to create output
	 * @return string[] List of URLs for the package files
	 */
	public function getFiles( string $type ) : array
	{
		$files = [];

		foreach( $this->registeredPackages as $filetype => $packageList )
		{
			if( $filetype === $type )
			{
				foreach( $packageList as $package )
				{
					foreach( $package->fileIncludes as $singleFile ) {
						$files[] = $this->basePath . $singleFile->path . $singleFile->text;
					}
				}
			}
		}

		return $files;
	}


	/**
	 * Returns the list of URLs for packages files with given filter.
	 *
	 * @param string $type Specific filetypes to create output
	 * @param string $version URL version string with %s placeholder for the file time
	 * @return string[] List of URLs for the package files
	 */
	public function getUrls( string $type, string $version = '?v=%s' ) : array
	{
		$files = [];

		foreach( $this->registeredPackages as $filetype => $packageList )
		{
			if( $filetype === $type )
			{
				foreach( $packageList as $package ) {
					$files = array_merge( $files, $this->getFileUrls( $package, $version ) );
				}
			}
		}

		return $files;
	}


	/**
	 * Returns HTML for packages files with given filter.
	 *
	 * @param string $type Specific filetypes to create output
	 * @return string HTML output with script and stylesheet link tags
	 */
	public function getHTML( string $type ) : string
	{
		$html = '';
		$version = '?v=%s';

		if( strpos( $this->baseURL, '?' ) !== false ) {
			$version = '&v=%s';
		}

		foreach( $this->getUrls( $type, $version ) as $file )
		{
			switch( $type )
			{
				case 'js':
					$html .= '<script type="text/javascript" src="' . $file . '"></script>' . PHP_EOL;
					break;
				case 'css':
					$html .= '<link rel="stylesheet" type="text/css" href="' . $file . '"/>' . PHP_EOL;
					break;
			}
		}

		return $html;
	}


	/**
	 * Returns the file URLs of the given package object.
	 *
	 * @param \stdClass $package Object with "fileIncludes" property containing a
	 * 	list of file objects with "path" and "text" properties
	 * @param string $version Version string that should be added to the URLs suitable for sprintf()
	 * @return string[] List of URLs to the files from the package
	 * @throws \Aimeos\MW\Jsb2\Exception If the file modification timestamp couldn't be determined
	 */
	protected function getFileUrls( \stdClass $package, string $version = '?v=%s' ) : array
	{
		$list = [];

		foreach( $package->fileIncludes as $singleFile )
		{
			$filename = $this->basePath . $singleFile->path . $singleFile->text;

			if( !is_file( $filename ) || ( $fileTime = filemtime( $filename ) ) === false ) {
				throw new \Aimeos\MW\Jsb2\Exception( sprintf( 'Unable to read filetime of file "%1$s"', $filename ) );
			}

			$list[] = $this->baseURL . $singleFile->path . $singleFile->text . sprintf( $version, $fileTime );
		}

		return $list;
	}


	/**
	 * Get the packages from a JSON decoded manifest and validates them.
	 *
	 * @param object JSON decoded manifest
	 * @param string[] $filter What packages should NOT be returned
	 */
	protected function getPackages( $manifest, array $filter = [] ) : array
	{
		$packageContainer = [];

		if( !isset( $manifest->pkgs ) || !is_array( $manifest->pkgs ) ) {
			throw new \Aimeos\MW\Jsb2\Exception( 'No packages found' );
		}

		foreach( $manifest->pkgs as $package )
		{
			if( !isset( $package->name ) || !isset( $package->file ) || !is_object( $package ) ) {
				throw new \Aimeos\MW\Jsb2\Exception( 'Invalid package content' );
			}

			if( !isset( $package->fileIncludes ) || !is_array( $package->fileIncludes ) ) {
				throw new \Aimeos\MW\Jsb2\Exception( 'No files in package found' );
			}

			if( !in_array( $package->name, $filter ) ) {
				$packageContainer[pathinfo( $package->file, PATHINFO_EXTENSION )][] = $package;
			}
		}

		return $packageContainer;
	}


	/**
	 * Returns the content of a manifest file.
	 *
	 * @param string $filepath Path to manifest
	 * @return object Manifest file content
	 * @throws \Aimeos\MW\Jsb2\Exception
	 */
	protected function getManifest( string $filepath )
	{
		if( !file_exists( $filepath ) ) {
			throw new \Aimeos\MW\Jsb2\Exception( sprintf( 'File does not exists: "%1$s"', $filepath ) );
		}

		if( ( $content = file_get_contents( $filepath ) ) === false ) {
			throw new \Aimeos\MW\Jsb2\Exception( sprintf( 'Unable to read content from "%1$s"', $filepath ) );
		}

		if( ( $content = json_decode( $content ) ) === null ) {
			throw new \Aimeos\MW\Jsb2\Exception( 'File content is not JSON encoded' );
		}

		return $content;
	}
}
