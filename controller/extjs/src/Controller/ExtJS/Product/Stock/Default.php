<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product stock controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Stock_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the product stock controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product_Stock' );

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'stock' );
	}


	/**
	 * Creates a new product stock item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product stock properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Creates a new product stock item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "product.stock" prefix
	 * @return MShop_Product_Item_Stock_Interface Product stock item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'product.stock.id': $item->setId( $value ); break;
				case 'product.stock.productid': $item->setProductId( $value ); break;
				case 'product.stock.warehouseid': $item->setWarehouseId( $value ); break;
				case 'product.stock.stocklevel':
					if( $value !== '' ) {
						$item->setStocklevel( $value );
					} else {
						$item->setStocklevel( null );
					}
					break;
				case 'product.stock.dateback':
					if( $value !== '' ) {
						$item->setDateBack( str_replace( 'T', ' ', $value ) );
					} else {
						$item->setDateBack( null );
					}
					break;
			}
		}

		return $item;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
