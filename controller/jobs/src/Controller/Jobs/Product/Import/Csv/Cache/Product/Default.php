<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Product cache for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Cache_Product_Default
	extends Controller_Jobs_Product_Import_Csv_Cache_Abstract
	implements Controller_Jobs_Product_Import_Csv_Cache_Interface
{
	private $_prodmap = array();


	/**
	 * Returns the product ID for the given code
	 *
	 * @param string $code Product code
	 * @param string|null $type Attribute type
	 * @return string|null Product ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->_prodmap[$code] ) ) {
			return $this->_prodmap[$code];
		}

		$manager = MShop_Factory::createManager( $this->_getContext(), 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->_prodmap[$code] = $item->getId();
			return $this->_prodmap[$code];
		}
	}


	/**
	 * Adds the product ID to the cache
	 *
	 * @param MShop_Common_Item_Interface $item Product object
	 */
	public function set( MShop_Common_Item_Interface $item )
	{
		$this->_prodmap[ $item->getCode() ] = $item->getId();
	}
}