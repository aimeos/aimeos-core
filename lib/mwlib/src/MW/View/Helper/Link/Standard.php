<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
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
		$list = \Aimeos\Map::explode( '/', $cfgkey )->skip( 2 );

		$target = $view->config( $cfgkey . '/target' );
		$cntl = $view->config( $cfgkey . '/controller', ucfirst( $list->shift() ) );
		$action = $view->config( $cfgkey . '/action', $list->shift() );
		$config = $view->config( $cfgkey . '/config', [] );
		$filter = $view->config( $cfgkey . '/filter', [] );

		$params = array_diff( $params, array_flip( $filter ) );

		return $view->url( $target, $cntl, $action, $params, $fragments, $config );
	}
}
