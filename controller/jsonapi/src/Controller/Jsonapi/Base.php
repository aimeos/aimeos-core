<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jsonapi
 */


namespace Aimeos\Controller\Jsonapi;


/**
 * JSON API common controller
 *
 * @package Controller
 * @subpackage Jsonapi
 */
class Base
{
	private $context;
	private $templatePaths;
	private $path;


	/**
	 * Initializes the controller
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $path )
	{
		$this->context = $context;
		$this->templatePaths = $templatePaths;
		$this->path = $path;
	}


	/**
	 * Returns the context item object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the paths to the template files
	 *
	 * @return array List of file system paths
	 */
	protected function getTemplatePaths()
	{
		return $this->templatePaths;
	}


	/**
	 * Returns the path to the controller
	 *
	 * @return string Controller path, e.g. "product/stock"
	 */
	protected function getPath()
	{
		return $this->path;
	}


	/**
	 * Returns the absolute path to the given template file.
	 * It uses the first one found from the configured paths in the manifest files, but in reverse order.
	 *
	 * @param string|array $default Relative file name or list of file names to use when nothing else is configured
	 * @param string $confpath Configuration key of the path to the template file
	 * @return string path the to the template file
	 * @throws \Aimeos\Controller\Jsonapi\Exception If no template file was found
	 */
	protected function getTemplate( $confpath, $default )
	{
		$ds = DIRECTORY_SEPARATOR;

		foreach( (array) $default as $fname )
		{
			$file = $this->context->getConfig()->get( $confpath, $fname );

			foreach( array_reverse( $this->templatePaths ) as $path => $relPaths )
			{
				foreach( $relPaths as $relPath )
				{
					$absPath = $path . $ds . $relPath . $ds . $file;
					if( $ds !== '/' ) {
						$absPath = str_replace( '/', $ds, $absPath );
					}

					if( is_file( $absPath ) ) {
						return $absPath;
					}
				}
			}
		}

		throw new \Aimeos\Controller\Jsonapi\Exception( sprintf( 'Template "%1$s" not available', $file ) );
	}
}
