<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	extends MShop_Common_Item_Interface, MShop_Common_Item_ListRef_Interface, MShop_Common_Item_Typeid_Interface
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
	 */
	public function setStatus($status);

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
	 */
	public function setCode($code);

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
	 */
	public function setLabel($label);

	/**
	 * Returns the starting point of time, in which the product is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart();

	/**
	 * Sets a new starting point of time, in which the product is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart($date);

	/**
	 * Returns the ending point of time, in which the product is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd();

	/**
	 * Sets a new ending point of time, in which the product is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd($date);

}
