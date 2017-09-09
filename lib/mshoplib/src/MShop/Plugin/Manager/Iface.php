<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Manager;


/**
 * Plugin manager interface
 * @package MShop
 * @subpackage Plugin
 */
interface Iface extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the plugin provider which is responsible for the plugin item
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
	 * @throws \Aimeos\MShop\Plugin\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item );

	/**
	 * Registers plugins to the given publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $publisher Publisher object
	 * @param string $type Unique plugin type code
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $publisher, $type );
}
