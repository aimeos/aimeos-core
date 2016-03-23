<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Decorator;


/**
 * Decorator interface for JSON API client
 *
 * @package Controller
 * @subpackage JsonAdm
 */
interface Iface
	extends \Aimeos\Admin\JsonAdm\Iface
{
	/**
	 * Initializes a new client decorator object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Iface $client Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @return void
	 */
	public function __construct( \Aimeos\Admin\JsonAdm\Iface $client,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path );
}