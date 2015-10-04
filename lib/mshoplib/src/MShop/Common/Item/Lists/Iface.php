<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for all list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Lists_Iface
	extends MShop_Common_Item_Iface, MShop_Common_Item_Config_Iface,
		MShop_Common_Item_Position_Iface, MShop_Common_Item_Time_Iface,
		MShop_Common_Item_Typeid_Iface
{
	/**
	 * Returns the parent Id (DB-field parentid) of the common list item, like the unique Id of a product or a tree node.
	 *
	 * @return integer Parent Id of the common list item
	 */
	public function getParentId();

	/**
	 * Sets the parent Id (DB-field paremntid) of the common list item, like the unique Id of a product or a tree node.
	 *
	 * @param integer $parentid New parent Id of the common list item
	 * @return void
	 */
	public function setParentId( $parentid );

	/**
	 * Returns the domain of the common list item, e.g. text or media.
	 *
	 * @return string Domain of the common list item
	 */
	public function getDomain();

	/**
	 * Sets the new domain of the common list item, e.g. text od media.
	 *
	 * @param string $domain New domain of the common list item
	 * @return void
	 */
	public function setDomain( $domain );

	/**
	 * Returns the reference id of the common list item, like the unique id of a text item or a media item.
	 *
	 * @return string reference id of the common list item
	 */
	public function getRefId();

	/**
	 * Sets the new reference id of the common list item, like the unique id of a text item or a media item.
	 *
	 * @param string $refid New reference id of the common list item
	 * @return void
	 */
	public function setRefId( $refid );

	/**
	 * Returns the status of the list item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the new status of the list item.
	 *
	 * @param integer $status Status of the item
	 * @return void
	 */
	public function setStatus( $status );

	/**
	 * Returns the referenced item if it's available.
	 *
	 * @return MShop_Common_Item_Iface|null Referenced list item
	 */
	public function getRefItem();

	/**
	 * Stores the item referenced by the list item.
	 *
	 * @param MShop_Common_Item_Iface $refItem Item referenced by the list item
	 * @return void
	 */
	public function setRefItem( MShop_Common_Item_Iface $refItem );
}
