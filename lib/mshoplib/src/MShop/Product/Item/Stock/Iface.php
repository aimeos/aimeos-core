<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item\Stock;


/**
 * Default product stock item interface.
 *
 * @package MShop
 * @subpackage Product
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Parentid\Iface
{
	/**
	 * Returns the warehouse Id.
	 *
	 * @return integer Warehouse Id
	 */
	public function getWarehouseId();

	/**
	 * Sets the Warehouse Id.
	 *
	 * @param integer $warehouseid New warehouse Id
	 * @return \Aimeos\MShop\Product\Item\Stock\Iface Product stock item for chaining method calls
	 */
	public function setWarehouseId( $warehouseid );

	/**
	 * Returns the stock level.
	 *
	 * @return integer Stock level
	 */
	public function getStocklevel();

	/**
	 * Sets the stock level.
	 *
	 * @param integer $stocklevel New stock level
	 * @return \Aimeos\MShop\Product\Item\Stock\Iface Product stock item for chaining method calls
	 */
	public function setStocklevel( $stocklevel );

	/**
	 * Returns the back in stock date of the product.
	 *
	 * @return string Back in stock date of the product
	 */
	public function getDateBack();

	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the product
	 * @return \Aimeos\MShop\Product\Item\Stock\Iface Product stock item for chaining method calls
	 */
	public function setDateBack( $backdate );
}