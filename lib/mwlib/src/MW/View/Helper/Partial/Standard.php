<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Partial;


/**
 * View helper class for rendering partials.
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Iface
{
	private $config;
	private $paths;


	/**
	 * Initializes the parital view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @param array $paths Associative list of base path / relative paths combinations
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Config\Iface $config, array $paths )
	{
		parent::__construct( $view );

		$this->config = $config;
		$this->paths = $paths;
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
		$file = $this->config->get( $confpath, $default );

		$view = clone $this->getView();
		$view->assign( $params );

		foreach( array_reverse( $this->paths ) as $path => $relPaths )
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

		throw new \Aimeos\MW\Exception( sprintf( 'Partial "%1$s" not available', $file ) );
	}
}
