<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Manager;


/**
 * Generic interface for stock managers
 * @package MShop
 * @subpackage Stock
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Decreases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the stock type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function decrease( array $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface;

	/**
	 * Increases the stock level for the given product codes/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product codes as keys and quantities as values
	 * @param string $type Unique code of the type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function increase( array $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface;

	/**
	 * Inserts the new stock item
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface $item Stock item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Stock\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Stock\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Stock\Item\Iface;
}
