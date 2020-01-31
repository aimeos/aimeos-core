<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Link;


/**
 * View helper class for building URLs in a simple way
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Link\Iface
{
	/**
	 * Returns the URL for the given parameter
	 *
	 * @param string $cfgkey Prefix of the configuration key for the URL settings
	 * @param array $params Associative list of parameters that should be part of the URL
	 * @param string[] $fragments Trailing URL fragment that are not relevant to identify the resource
	 * @return string Complete URL that can be used in the template
	 */
	public function transform( string $cfgkey, array $params = [], array $fragments = [] ) : string
	{
		$view = $this->getView();

		$target = $view->config( $cfgkey . '/target' );
		$cntl = $view->config( $cfgkey . '/controller' );
		$action = $view->config( $cfgkey . '/action' );
		$config = $view->config( $cfgkey . '/config', [] );

		return $view->url( $target, $cntl, $action, $params, $fragments, $config );
	}
}
