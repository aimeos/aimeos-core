<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Default product stock item interface.
 *
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Stock_Interface extends MShop_Common_Item_Interface
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
	 */
	public function setProductId($prodid);

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
	 */
	public function setWarehouseId($warehouseid);

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
	 */
	public function setStocklevel($stocklevel);

	/**
	 * Returns the back in stock date of the product.
	 *
	 * @return string Back in stock date of the product
	 */
	public function getDateBack();

	/**
	 * Sets the product back in stock date.
	 *
	 * @param string $dateBack New back in stock date of the product
	 */
	public function setDateBack($backdate);

}