<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Default tag item implementation
 *
 * @package MShop
 * @subpackage Product
 */
interface MShop_Product_Item_Tag_Interface extends MShop_Common_Item_Interface
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
	 */
	public function setLanguageId( $id );

	/**
	 * Returns the type id of the tag item
	 *
	 * @return string Type ID of the tag item
	 */
	public function getTypeId();

	/**
	 * Sets the Type Id of the tag item
	 *
	 * @param string $id New Type ID of the tag item
	 */
	public function setTypeId( $id );

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
	 */
	public function setLabel( $label );

	/**
	 * Returns the type code of the tag item.
	 *
	 * @return string Type code of the tag item
	 */
	public function getType();
}
