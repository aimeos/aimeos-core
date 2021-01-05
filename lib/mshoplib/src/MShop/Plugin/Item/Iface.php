<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Item;


/**
 * Generic interface for plugins created and saved by plugin managers.
 *
 * @package MShop
 * @subpackage Plugin
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\Position\Iface, \Aimeos\MShop\Common\Item\Status\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the name of the plugin item.
	 *
	 * @return string Label of the plugin item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the plugin item.
	 *
	 * @param string $label New label of the plugin item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Plugin\Item\Iface;

	/**
	 * Returns the provider of the plugin.
	 *
	 * @return string Plugin provider which is the short plugin class name
	 */
	public function getProvider() : string;

	/**
	 * Sets the new provider of the plugin item which is the short name of the plugin class name.
	 *
	 * @param string $provider Plugin provider, esp. short plugin class name
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Plugin\Item\Iface;
}
