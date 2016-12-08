<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the code of the stock item.
	 *
	 * @return string Product code (SKU)
	 */
	public function getProductCode();

	/**
	 * Sets a new code of the stock item.
	 *
	 * @param string $code New product code (SKU)
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setProductCode( $code );

	/**
	 * Returns the stock level.
	 *
	 * @return integer|null Stock level
	 */
	public function getStocklevel();

	/**
	 * Sets the stock level.
	 *
	 * @param integer $stocklevel New stock level
	 * @return \Aimeos\MShop\Stock\Item\Stock\Iface Stock stock item for chaining method calls
	 */
	public function setStocklevel( $stocklevel );

	/**
	 * Returns the back in stock date of the stock.
	 *
	 * @return string|null Back in stock date of the stock
	 */
	public function getDateBack();

	/**
	 * Sets the stock back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the stock
	 * @return \Aimeos\MShop\Stock\Item\Stock\Iface Stock stock item for chaining method calls
	 */
	public function setDateBack( $backdate );
}