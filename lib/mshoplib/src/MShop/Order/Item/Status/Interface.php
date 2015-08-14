<?php 
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */

/**
 * Generic interface for order status.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Status_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the parentid of the order status.
	 *
	 * @return integer Parent ID of the order status
	 */
	public function getParentId();
	
	/**
	 * Sets the parentid of the order status.
	 *
	 * @param integer $parentid Parent ID of the order status
	 * @return void
	 */
	public function setParentId($parentid);
	
	
	/**
	 * Returns the type of the order status.
	 *
	 * @return string Type of the order status
	 */
	public function getType();
	
	/**
	 * Sets the type of the order status.
	 *
	 * @param string $type Type of the order status
	 * @return void
	 */
	public function setType( $type );
	
	/**
	 * Returns the value of the order status.
	 *
	 * @return string Value of the order status
	 */
	public function getValue();
	
	/**
	 * Sets the value of the order status.
	 *
	 * @param string $value Value of the order status
	 * @return void
	 */
	public function setValue( $value );
	
	
}