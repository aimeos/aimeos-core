<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product stock warehouse item interface.
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Stock_Warehouse_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the warehouse item.
	 *
	 * @return string Code of the warehouse item
	 */
	public function getCode();

	/**
	 * Sets the code of the warehouse item.
	 *
	 * @param string $code New Code of the warehouse item
	 */
	public function setCode( $code );

	/**
	 * Returns the label of the warehouse item.
	 *
	 * @return string Label of the warehouse item
	 */
	public function getLabel();

	/**
	 * Sets the label of the warehouse item.
	 *
	 * @param string $label New label of the warehouse item
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the warehouse item.
	 *
	 * @return string Status of the warehouse item
	 */
	public function getStatus();

	/**
	 * Sets the status of the warehouse item.
	 *
	 * @param integer $status New status of the warehouse item
	 */
	public function setStatus( $status );

}