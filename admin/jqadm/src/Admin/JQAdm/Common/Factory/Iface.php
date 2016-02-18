<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Factory;


/**
 * Generic interface for all JQAdm client factories.
 *
 * @package Admin
 * @subpackage JQAdm
 */
interface Iface
{
	/**
	 *	Creates a client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $name Admin name (from configuration or "Standard" if null)
	 * @return \Aimeos\Admin\JQAdm\Iface New client object
	 */
	public static function createClient( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $name = null );
}
