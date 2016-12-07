<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Manager;


/**
 * Shop interface with methods for setting locale.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code (optional)
	 * @param string $currency Currency code (optional)
	 * @param boolean $active Flag to get only active items (optional)
	 * @param integer|null $level Constant from abstract class which site ID levels should be available (optional),
	 * 	based on config or value for SITE_PATH if null
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for the given parameters
	 * @throws \Aimeos\MShop\Locale\Exception If no locale item is found
	 */
	public function bootstrap( $site, $lang = '', $currency = '', $active = true, $level = null );
}