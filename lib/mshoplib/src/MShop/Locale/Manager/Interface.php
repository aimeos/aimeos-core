<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Shop interface with methods for setting locale.
 *
 * @package MShop
 * @subpackage Locale
 */
interface MShop_Locale_Manager_Interface
	extends MShop_Common_Manager_Factory_Interface
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
	 * @return MShop_Locale_Item_Interface Locale item for the given parameters
	 * @throws MShop_Locale_Exception If no locale item is found
	 */
	public function bootstrap( $site, $lang = '', $currency = '', $active = true, $level = null );
}