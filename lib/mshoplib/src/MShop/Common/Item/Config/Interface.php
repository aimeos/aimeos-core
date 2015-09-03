<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Common
 */


/**
 * Common interface for items containing configuration
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Config_Interface
{
	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig();

	/**
	 * Sets the configuration values of the item
	 *
	 * @param array $config Configuration values
	 * @return void
	 */
	public function setConfig( array $config );
}
