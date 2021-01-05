<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code (optional)
	 * @param string $currency Currency code (optional)
	 * @param bool $active Flag to get only active items (optional)
	 * @param int|null $level Constant from abstract class which site ID levels should be available (optional),
	 * 	based on config or value for SITE_PATH if null
	 * @param bool $bare Allow locale items with sites only
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item for the given parameters
	 * @throws \Aimeos\MShop\Locale\Exception If no locale item is found
	 */
	public function bootstrap( string $site, string $lang = '', string $currency = '', bool $active = true,
		int $level = null, bool $bare = false ) : \Aimeos\MShop\Locale\Item\Iface;
}
