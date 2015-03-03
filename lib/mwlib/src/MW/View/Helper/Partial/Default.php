<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for rendering partials.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Partial_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_config;
	private $_paths;


	/**
	 * Initializes the parital view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function __construct( MW_View_Interface $view, MW_Config_Interface $config, array $paths )
	{
		parent::__construct( $view );

		$this->_config = $config;
		$this->_paths = $paths;
	}


	/**
	 * Returns the rendered partial.
	 *
	 * @param string $confpath Name of the config key of the template
	 * @param string $default Default template name if config key is not available
	 * @param array $params Associative list of key/value pair that should be available in the partial
	 * @return string Rendered partial content
	 */
	public function transform( $confpath, $default, array $params = array() )
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = $this->_config->get( $confpath, $default );

		$view = clone $this->_getView();
		$view->assign( $params );

		foreach( array_reverse( $this->_paths ) as $path => $relPaths )
		{
			foreach( $relPaths as $relPath )
			{
				$absPath = $path . $ds . $relPath . $ds . $file;
				if( $ds !== '/' ) {
					$absPath = str_replace( '/', $ds, $absPath );
				}

				if( is_file( $absPath ) ) {
					return $view->render( $absPath );
				}
			}
		}

		throw new MW_Exception( sprintf( 'Partial "%1$s" not available', $file ) );
	}
}
