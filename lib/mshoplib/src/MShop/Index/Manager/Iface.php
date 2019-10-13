<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Index index interface for classes managing product indices.
 *
 * @package MShop
 * @subpackage Index
 */
interface Iface
	extends \Aimeos\MShop\Product\Manager\Iface
{
	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return integer[] List of ID values as key and the number of counted products as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key );


	/**
	 * Optimizes the index if necessary.
	 * This operation can last very long and it shouldn't be called by a script
	 * executed by a web server.
	 *
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function optimize();


	/**
	 * Removes all entries not touched after the given timestamp in the index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function cleanupIndex( $timestamp );


	/**
	 * Rebuilds the index for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface[] $items Associative list of product IDs as keys and items as values
	 * @return \Aimeos\MShop\Index\Manager\Iface Manager object for chaining method calls
	 */
	public function rebuildIndex( array $items = [] );

	/**
	 * Stores a new item into the index
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item
	 * @param boolean $fetch True if the new ID should be set in the item
	 * @return \Aimeos\MShop\Product\Item\Iface Saved item
	 */
	public function saveItem( \Aimeos\MShop\Product\Item\Iface $item, $fetch = true );
}
