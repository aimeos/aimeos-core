<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Interface for order frontend controllers.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Order_Interface
	extends Controller_Frontend_Common_Interface
{
	/**
	 * Creates a new order from the given basket.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object to be stored
	 * @return MShop_Order_Item_Interface Order item that belongs to the stored basket
	 */
	public function store( MShop_Order_Item_Base_Interface $basket );
}
