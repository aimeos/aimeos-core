<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Attribute cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Cache_Base
{
	private $context;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Returns the context object
	 *
	 * @return MShop_Context_Item_Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}
}