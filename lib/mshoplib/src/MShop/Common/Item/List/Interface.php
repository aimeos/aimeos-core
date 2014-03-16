<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for all list items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_List_Interface
	extends MShop_Common_Item_Interface, MShop_Common_Item_Position_Interface, MShop_Common_Item_Typeid_Interface
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
	 */
	public function setRefId( $refid );

	/**
	 * Returns the start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string Start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateStart();

	/**
	 * Sets the new start date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New start date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateStart( $date );

	/**
	 * Returns the end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @return string End date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function getDateEnd();

	/**
	 * Sets the new end date of the common list item (YYYY-MM-DD hh:mm:ss).
	 *
	 * @param string $date New end date of the common list item (YYYY-MM-DD hh:mm:ss)
	 */
	public function setDateEnd( $date );

	/**
	 * Returns the configuration of the list item.
	 *
	 * @return string Custom configuration values
	 */
	public function getConfig();

	/**
	 * Sets the new configuration for the list item.
	 *
	 * @param array $config Custom configuration values
	 */
	public function setConfig( array $config );

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
	 */
	public function setStatus( $status );

	/**
	 * Returns the referenced item if it's available.
	 *
	 * @return MShop_Common_Item_Interface|null Referenced list item
	 */
	public function getRefItem();

	/**
	 * Stores the item referenced by the list item.
	 *
	 * @param MShop_Common_Item_Interface $refItem Item referenced by the list item
	 */
	public function setRefItem( MShop_Common_Item_Interface $refItem );
}
