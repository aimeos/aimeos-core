<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Position\Iface,
	\Aimeos\MShop\Common\Item\Typeid\Iface, \Aimeos\MShop\Common\Item\Config\Iface
{
	/**
	 * Returns the name of the plugin item.
	 *
	 * @return string Label of the plugin item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the plugin item.
	 *
	 * @param string $label New label of the plugin item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setLabel( $label );

	/**
	 * Returns the provider of the plugin.
	 *
	 * @return string Plugin provider which is the short plugin class name
	 */
	public function getProvider();

	/**
	 * Sets the new provider of the plugin item which is the short name of the plugin class name.
	 *
	 * @param string $provider Plugin provider, esp. short plugin class name
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setProvider( $provider );

	/**
	 * Returns the status of the plugin item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the new status of the plugin item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Plugin\Item\Iface Plugin item for chaining method calls
	 */
	public function setStatus( $status );
}
