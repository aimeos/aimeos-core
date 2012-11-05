<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 589 2012-04-25 15:24:23Z nsendetzky $
 */


/**
 * Generic interface for all site items.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Site_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the value of the common site item.
	 *
	 * @return integer Value of the common site item
	 */
	public function getValue();
	
	/**
	 * Sets the value of the common site item.
	 *
	 * @param integer $value New value of the common site item
	 */
	public function setValue( $value );
}