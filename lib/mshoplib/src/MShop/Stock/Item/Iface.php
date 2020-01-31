<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	 * Returns the code of the stock item.
	 *
	 * @return string Product code (SKU)
	 */
	public function getProductCode() : string;

	/**
	 * Sets a new code of the stock item.
	 *
	 * @param string $code New product code (SKU)
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setProductCode( string $code ) : \Aimeos\MShop\Stock\Item\Iface;

	/**
	 * Returns the stock level.
	 *
	 * @return int|null Stock level
	 */
	public function getStockLevel() : ?int;

	/**
	 * Sets the stock level.
	 *
	 * @param string|int|double|null $stocklevel New stock level
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
	public function setDateBack( string $backdate = null ) : \Aimeos\MShop\Stock\Item\Iface;

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
	public function setTimeframe( string $timeframe ) : \Aimeos\MShop\Stock\Item\Iface;
}
