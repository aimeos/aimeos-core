<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Site;


/**
 * Common interface for all Site items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\Tree\Iface
{
	/**
	 * Sets the ID of the site.
	 *
	 * @param string $value Unique ID of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setSiteId( string $value ) : \Aimeos\MShop\Locale\Item\Site\Iface;

	/**
	 * Returns the icon path of the site.
	 *
	 * @return string Returns the icon of the site
	 */
	public function getIcon() : string;

	/**
	 * Sets the icon path of the site.
	 *
	 * @param string $value The icon of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setIcon( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Returns the logo path of the site.
	 *
	 * @param bool $large Return the largest image instead of the smallest
	 * @return string Returns the logo of the site
	 */
	public function getLogo( bool $large = false ) : string;

	/**
	 * Returns the logo path of the site.
	 *
	 * @return string Returns the logo of the site
	 */
	public function getLogos() : array;

	/**
	 * Sets the logo path of the site.
	 *
	 * @param string $value The logo of the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLogo( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Sets the logo path of the site.
	 *
	 * @param array $value List of logo URLs with widths of the media file in pixels as keys
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setLogos( array $value ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Returns the supplier ID related to the site.
	 *
	 * @return string Returns the supplier ID related to the site
	 */
	public function getSupplierId() : string;

	/**
	 * Sets the supplier ID related to the site.
	 *
	 * @param string $value The supplier ID related to the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setSupplierId( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface;

	/**
	 * Returns the theme name for the site.
	 *
	 * @return string Returns the theme name for the site or empty for default theme
	 */
	public function getTheme() : string;

	/**
	 * Sets the theme name for the site.
	 *
	 * @param string $value The theme name for the site
	 * @return \Aimeos\MShop\Locale\Item\Site\Iface Locale site item for chaining method calls
	 */
	public function setTheme( string $value ) : \Aimeos\MShop\Common\Item\Tree\Iface;
}
