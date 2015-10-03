<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


/**
 * Generic interface for product stock objects.
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Manager_Stock_Iface
	extends MShop_Common_Manager_Factory_Iface
{
	/**
	 * Decreases the stock level of the product for the warehouse.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $warehouseCode Unique code of the warehouse
	 * @param integer $amount Amount the stock level should be decreased
	 * @return void
	 */
	public function decrease( $productCode, $warehouseCode, $amount );


	/**
	 * Increases the stock level of the product for the warehouse.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $warehouseCode Unique code of the warehouse
	 * @param integer $amount Amount the stock level should be increased
	 * @return void
	 */
	public function increase( $productCode, $warehouseCode, $amount );
}