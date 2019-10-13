<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item, $type );

	/**
	 * Registers plugins to the given publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $publisher Publisher object
	 * @param string $type Plugin type code
	 * @return \Aimeos\MShop\Plugin\Manager\Iface Manager object for chaining method calls
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $publisher, $type );

	/**
	 * Saves a new or modified plugin to the storage.
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Plugin\Item\Iface $item, $fetch = true );
}
