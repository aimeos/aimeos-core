<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Item;


/**
 * Generic interface for catalog items.
 *
 * @package MShop
 * @subpackage Catalog
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Tree\Iface
{
	/**
	 * Returns the URL segment for the catalog item.
	 *
	 * @return string URL segment of the catalog item
	 */
	public function getUrl() : string;

	/**
	 * Sets a new URL segment for the catalog.
	 *
	 * @param string|null $url New URL segment of the catalog item
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Catalog\Item\Iface;

	/**
	 * Returns the URL target specific for that category
	 *
	 * @return string URL target specific for that category
	 */
	public function getTarget() : string;

	/**
	 * Sets a new URL target specific for that category
	 *
	 * @param string $value New URL target specific for that category
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Catalog\Item\Iface;
}
