<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Customer
 */


/**
 * Interface for customer group objects
 *
 * @package MShop
 * @subpackage Customer
 */
interface MShop_Customer_Item_Group_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the customer group
	 *
	 * @return string Code of the customer group
	 */
	public function getCode();

	/**
	 * Sets the new code of the customer group
	 *
	 * @param string $value Code of the customer group
	 * @return void
	 */
	public function setCode( $value );

	/**
	 * Returns the label of the customer group
	 *
	 * @return string Label of the customer group
	 */
	public function getLabel();

	/**
	 * Sets the new label of the customer group
	 *
	 * @param string $value Label of the customer group
	 * @return void
	 */
	public function setLabel( $value );
}
