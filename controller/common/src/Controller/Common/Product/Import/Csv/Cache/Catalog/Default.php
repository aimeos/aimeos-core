<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Category cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Cache_Catalog_Default
	extends Controller_Common_Product_Import_Csv_Cache_Base
	implements Controller_Common_Product_Import_Csv_Cache_Iface
{
	private $categories = array();


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context );

		$manager = MShop_Factory::createManager( $context, 'catalog' );
		$result = $manager->searchItems( $manager->createSearch() );

		foreach( $result as $id => $item ) {
			$this->categories[ $item->getCode() ] = $id;
		}
	}


	/**
	 * Returns the catalog ID for the given code and type
	 *
	 * @param string $code Category code
	 * @param string|null $type Not used
	 * @return string|null Catalog ID or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->categories[$code] ) ) {
			return $this->categories[$code];
		}

		$manager = MShop_Factory::createManager( $this->getContext(), 'catalog' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->categories[$code] = $item->getId();
			return $item->getId();
		}
	}


	/**
	 * Adds the catalog item to the cache
	 *
	 * @param MShop_Common_Item_Iface $item Catalog object
	 */
	public function set( MShop_Common_Item_Iface $item )
	{
		$this->categories[ $item->getCode() ] = $item->getId();
	}
}