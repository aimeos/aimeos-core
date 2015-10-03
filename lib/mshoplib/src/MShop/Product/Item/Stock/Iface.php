<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product stock item interface.
 *
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Stock_Iface extends MShop_Common_Item_Iface
{
	/**
	 * Returns the product Id.
	 *
	 * @return integer Product Id
	 */
	public function getProductId();

	/**
	 * Sets the product Id.
	 *
	 * @param integer $prodid New product Id
	 * @return void
	 */
	public function setProductId( $prodid );

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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setDateBack( $backdate );

}