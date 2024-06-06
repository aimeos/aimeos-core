<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
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
 * @deprecated 2025.01
 */
class Standard
{
	private array $registeredPackages;
	private string $basePath;
	private string $baseURL;


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
	 * @param string $name File name, e.g. "index.js", "index.css", "ltr.index.css" or "rtl.index.css"
	 * @return string[] List of URLs for the package files
	 */
	public function getFiles( string $name ) : array
	{
		$files = [];

		foreach( $this->registeredPackages[$name] ?? [] as $package )
		{
			foreach( $package->fileIncludes as $singleFile ) {
				$files[] = $this->basePath . $singleFile->path . $singleFile->text;
			}
		}

		return $files;
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

			if( $package->overwrite ?? false )
			{
				if( !in_array( $package->name, $filter ) ) {
					$packageContainer[$package->file][] = $package;
				}
			}
			else
			{
				$packageContainer[$package->file] = [$package];
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
