<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Config;


/**
 * Common interface for items containing configuration
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
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
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setConfig( array $config );
}
