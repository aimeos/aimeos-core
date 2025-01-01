<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Manager;


/**
 * Plugin manager interface
 * @package MShop
 * @subpackage Plugin
 */
interface Iface extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Returns the plugin provider which is responsible for the plugin item
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @param string $type Plugin type code
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
	 * @throws \Aimeos\MShop\Plugin\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item, string $type ) : \Aimeos\MShop\Plugin\Provider\Iface;

	/**
	 * Registers plugins to the given publisher.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $publisher Publisher object
	 * @param string $type Plugin type code
	 * @return \Aimeos\MShop\Plugin\Manager\Iface Manager object for chaining method calls
	 */
	public function register( \Aimeos\MShop\Order\Item\Iface $publisher, string $type ) : \Aimeos\MShop\Plugin\Manager\Iface;
}
