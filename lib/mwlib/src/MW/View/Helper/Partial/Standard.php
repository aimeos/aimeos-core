<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	implements \Aimeos\MW\View\Helper\Partial\Iface
{
	/**
	 * Returns the rendered partial.
	 *
	 * @param string $file Relative path to the template
	 * @param array $params Associative list of key/value pair that should be available in the partial
	 * @return string Rendered partial content
	 */
	public function transform( $file, array $params = [] )
	{
		$view = clone $this->getView();
		$view->assign( $params );

		return $view->render( $file );
	}
}
