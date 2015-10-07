<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Factory;


/**
 * Generic interface for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 *	Creates a manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string $name Manager name (from configuration or "Default" if null)
	 * @return \Aimeos\MShop\Common\Manager\Iface New manager object
	 */
	public static function createManager( \Aimeos\MShop\Context\Item\Iface $context, $name = null );
}
