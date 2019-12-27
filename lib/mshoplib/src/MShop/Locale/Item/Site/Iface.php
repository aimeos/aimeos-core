<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
}
