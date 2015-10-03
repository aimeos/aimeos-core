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
class Controller_Common_Product_Import_Csv_Cache_Attribute_Default
	extends Controller_Common_Product_Import_Csv_Cache_Base
	implements Controller_Common_Product_Import_Csv_Cache_Iface
{
	private $attributes = array();


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context );

		$manager = MShop_Factory::createManager( $context, 'attribute' );
		$result = $manager->searchItems( $manager->createSearch() );

		foreach( $result as $id => $item ) {
			$this->attributes[ $item->getCode() ][ $item->getType() ] = $item;
		}
	}


	/**
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string|null $type Attribute type
	 * @return MShop_Attribute_Item_Iface|null Attribute object or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->attributes[$code] ) && isset( $this->attributes[$code][$type] ) ) {
			return $this->attributes[$code][$type];
		}

		$manager = MShop_Factory::createManager( $this->getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $code ),
			$search->compare( '==', 'attribute.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->attributes[$code][$type] = $item;
			return $item;
		}
	}


	/**
	 * Adds the attribute item to the cache
	 *
	 * @param MShop_Common_Item_Iface $item Attribute object
	 */
	public function set( MShop_Common_Item_Iface $item )
	{
		$code = $item->getCode();

		if( !isset( $this->attributes[$code] ) || !is_array( $this->attributes[$code] ) ) {
			$this->attributes[$code] = array();
		}

		$this->attributes[$code][ $item->getType() ] = $item;
	}
}