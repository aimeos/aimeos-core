<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @param string|null $name Manager name (from configuration or "Standard" if null)
	 * @return \Aimeos\MShop\Common\Manager\Iface New manager object
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface;
}
