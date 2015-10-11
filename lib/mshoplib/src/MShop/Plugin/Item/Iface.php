<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the type of the plugin.
	 *
	 * @return string Plugin type
	 */
	public function getType();

	/**
	 * Returns the type ID of the plugin.
	 *
	 * @return integer Plugin type ID
	 */
	public function getTypeId();

	/**
	 * Sets the new type ID of the plugin item.
	 *
	 * @param integer $typeid New plugin type ID
	 * @return void
	 */
	public function setTypeId( $typeid );

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
	 * @return void
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
	 * @return void
	 */
	public function setProvider( $provider );

	/**
	 * Returns the configuration of the plugin item.
	 *
	 * @return array Custom configuration values
	 */
	public function getConfig();

	/**
	 * Sets the new configuration for the plugin item.
	 *
	 * @param array $config Custom configuration values
	 * @return void
	 */
	public function setConfig( array $config );

	/**
	 * Returns the position of the plugin item.
	 *
	 * @return integer Position of the item
	 */
	public function getPosition();

	/**
	 * Sets the new position of the plugin item.
	 *
	 * @param integer $position Position of the item
	 * @return void
	 */
	public function setPosition( $position );

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
	 * @return void
	 */
	public function setStatus( $status );
}
