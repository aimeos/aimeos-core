<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14432 2011-12-19 09:30:25Z nsendetzky $
 */


/**
 * Generic interface for all items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Interface
{
	/**
	 * Returns the unique ID of the item.
	 *
	 * @return integer ID of the item
	 */
	public function getId();

	/**
	 * Sets the unique ID of the item.
	 *
	 * @param integer $id Unique ID of the item
	 */
	public function setId( $id );

	/**
	 * Returns the ID of the site the item is stored
	 *
	 * @return integer|null Site ID (or null if not available)
	 */
	public function getSiteId();

	/**
	 * Returns the create date of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated();
	
	/**
	 * Returns the time of last modification.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeModified();
	
	/**
	 * Returns the user code of user who created/modified the item at last.
	 *
	 * @return string Usercode of user who created/modified the item at last
	 */
	public function getEditor();
	
	/**
	 * Tests if the item was modified.
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified();

	/**
	 * Returns an associative list of item properties.
	 *
	 * @return array List of item properties.
	 */
	public function toArray();
}
