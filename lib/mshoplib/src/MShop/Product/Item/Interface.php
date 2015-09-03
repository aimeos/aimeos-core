<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Product
 */


/**
 * Generic interface for product items created and saved by product managers.
 *
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Interface
	extends MShop_Common_Item_Config_Interface, MShop_Common_Item_ListRef_Interface,
		MShop_Common_Item_Time_Interface, MShop_Common_Item_Typeid_Interface
{
	/**
	 * Returns the status of the product item.
	 *
	 * @return integer Status of the product
	 */
	public function getStatus();

	/**
	 * Sets the new status of the product item.
	 *
	 * @param integer $status New status of the product
	 * @return void
	 */
	public function setStatus( $status );

	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product
	 */
	public function getCode();

	/**
	 * Sets a new code of the product item.
	 *
	 * @param string $code New code of the product item
	 * @return void
	 */
	public function setCode( $code );

	/**
	 * Returns the supplier code of the product.
	 *
	 * @return string Supplier code of the product
	 */
	public function getSupplierCode();

	/**
	 * Sets a new supplier code of the product item.
	 *
	 * @param string $suppliercode New supplier code of the product
	 * @return void
	 */
	public function setSupplierCode( $suppliercode );

	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel();

	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 * @return void
	 */
	public function setLabel( $label );
}
