<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common interface for items that carry sorting informations.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Position_Interface
{
	/**
	 * Returns the position of the item in the list.
	 *
	 * @return integer Position of the item in the list
	 */
	public function getPosition();

	/**
	 * Sets the new position of the item in the list.
	 *
	 * @param integer $pos position of the item in the list
	 */
	public function setPosition( $pos );
}
