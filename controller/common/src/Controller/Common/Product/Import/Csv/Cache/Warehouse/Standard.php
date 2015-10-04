<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Warehouse cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Cache_Warehouse_Standard
	extends Controller_Common_Product_Import_Csv_Cache_Base
	implements Controller_Common_Product_Import_Csv_Cache_Iface
{
	private $warehouses = array();


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context );

		$manager = MShop_Factory::createManager( $context, 'product/stock/warehouse' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1000 );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$this->warehouses[ $item->getCode() ] = $id;
		}
	}


	/**
	 * Returns the warehouse ID for the given code
	 *
	 * @param string $code Warehouse code
	 * @param string|null $type Attribute type
	 * @return string|null Warehouse ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->warehouses[$code] ) ) {
			return $this->warehouses[$code];
		}
	}


	/**
	 * Adds the warehouse ID to the cache
	 *
	 * @param MShop_Common_Item_Iface $item Warehouse object
	 */
	public function set( MShop_Common_Item_Iface $item )
	{
		$this->warehouses[ $item->getCode() ] = $item->getId();
	}
}