<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * End point for the CSV import processors
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Processor_Done
	implements Controller_Common_Product_Import_Csv_Processor_Iface
{
	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Common_Product_Import_Csv_Processor_Iface $processor Decorated processor
	 */
	public function __construct( MShop_Context_Item_Iface $context, array $mapping,
		Controller_Common_Product_Import_Csv_Processor_Iface $processor = null )
	{
	}


	/**
	 * Saves the product related data to the storage
	 *
	 * @param MShop_Product_Item_Iface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Iface $product, array $data )
	{
		return $data;
	}
}
