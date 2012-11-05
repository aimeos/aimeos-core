<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Supplier
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
interface MShop_Supplier_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the label of the supplier item.
	 *
	 * @return string label of the supplier item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 */
	public function setLabel($value);

	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode();

	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 */
	public function setCode($value);

	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the status of the item
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus($value);
}
