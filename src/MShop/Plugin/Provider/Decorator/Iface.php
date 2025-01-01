<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


/**
 * Plugin decorator interface for dealing with run-time loadable extensions.
 *
 * @package MShop
 * @subpackage Plugin
 */
interface Iface extends \Aimeos\MShop\Plugin\Provider\Iface
{
	/**
	 * Initializes the plugin decorator object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Plugin\Item\Iface $item,
		\Aimeos\MShop\Plugin\Provider\Iface $provider );
}
