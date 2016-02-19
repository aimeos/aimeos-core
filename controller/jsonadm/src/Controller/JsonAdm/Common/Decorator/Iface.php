<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Common\Decorator;


/**
 * Decorator interface for JSON API controller
 *
 * @package Controller
 * @subpackage JsonAdm
 */
interface Iface
	extends \Aimeos\Controller\JsonAdm\Iface
{
	/**
	 * Initializes a new controller decorator object
	 *
	 * @param \Aimeos\Controller\JsonAdm\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return void
	 */
	public function __construct( \Aimeos\Controller\JsonAdm\Iface $controller,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path );
}