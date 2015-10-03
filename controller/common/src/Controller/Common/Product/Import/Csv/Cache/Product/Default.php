<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Product cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Cache_Product_Default
	extends Controller_Common_Product_Import_Csv_Cache_Base
	implements Controller_Common_Product_Import_Csv_Cache_Iface
{
	private $prodmap = array();


	/**
	 * Returns the product ID for the given code
	 *
	 * @param string $code Product code
	 * @param string|null $type Attribute type
	 * @return string|null Product ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->prodmap[$code] ) ) {
			return $this->prodmap[$code];
		}

		$manager = MShop_Factory::createManager( $this->getContext(), 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->prodmap[$code] = $item->getId();
			return $this->prodmap[$code];
		}
	}


	/**
	 * Adds the product ID to the cache
	 *
	 * @param MShop_Common_Item_Iface $item Product object
	 */
	public function set( MShop_Common_Item_Iface $item )
	{
		$this->prodmap[ $item->getCode() ] = $item->getId();
	}
}