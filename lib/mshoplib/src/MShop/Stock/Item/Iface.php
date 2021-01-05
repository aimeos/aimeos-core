<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Item;


/**
 * Default stock item interface.
 *
 * @package MShop
 * @subpackage Stock
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the ID of the product the stock item belongs to.
	 *
	 * @return string Product ID
	 */
	public function getProductId() : string;

	/**
	 * Sets a new product ID the stock item belongs to.
	 *
	 * @param string $value New product ID
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setProductId( string $value ) : \Aimeos\MShop\Stock\Item\Iface;

	/**
	 * Returns the stock level.
	 *
	 * @return int|null Stock level
	 */
	public function getStockLevel() : ?int;

	/**
	 * Sets the stock level.
	 *
	 * @param int|null $stocklevel New stock level
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
	 */
	public function setStockLevel( $stocklevel = null ) : \Aimeos\MShop\Stock\Item\Iface;

	/**
	 * Returns the back in stock date of the stock.
	 *
	 * @return string|null Back in stock date of the stock
	 */
	public function getDateBack() : ?string;

	/**
	 * Sets the stock back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the stock
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
	 */
	public function setDateBack( ?string $backdate ) : \Aimeos\MShop\Stock\Item\Iface;

	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe() : string;

	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : \Aimeos\MShop\Stock\Item\Iface;
}
