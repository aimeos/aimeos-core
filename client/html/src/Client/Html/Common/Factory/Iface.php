<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Factory;


/**
 * Generic interface for all HTML client factories.
 *
 * @package Client
 * @subpackage Html
 */
interface Iface
{
	/**
	 *	Creates a client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $name Client name (from configuration or "Default" if null)
	 * @return \Aimeos\Client\Html\Iface New client object
	 */
	public static function createClient( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $name = null );
}
