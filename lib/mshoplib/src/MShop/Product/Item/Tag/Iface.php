<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


/**
 * Default tag item implementation
 *
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Tag_Iface
	extends MShop_Common_Item_Iface, MShop_Common_Item_Typeid_Iface
{
	/**
	 * Returns the language id of the tag item
	 *
	 * @return string Language ID of the tag item
	 */
	public function getLanguageId();

	/**
	 * Sets the Language Id of the tag item
	 *
	 * @param string $id New Language ID of the tag item
	 * @return void
	 */
	public function setLanguageId( $id );

	/**
	 * Returns the label of the tag item.
	 *
	 * @return string Label of the tag item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the tag item.
	 *
	 * @param string $label Label of the tag item
	 * @return void
	 */
	public function setLabel( $label );

}
