<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Common\Factory;


/**
 * JSON API controller factory interface
 *
 * @package Controller
 * @subpackage JsonAdm
 */
interface Iface
{
	/**
	 * Creates a new controller based on the name
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @param string|null $name Name of the controller implementation ("Standard" if null)
	 * @return Aimeos\Controller\JsonAdm\Iface Controller Interface
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $path, $name = null );
}