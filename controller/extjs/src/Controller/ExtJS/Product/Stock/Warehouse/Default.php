<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product stock warehouse controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Stock_Warehouse_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the product stock warehouse controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product_Stock_Warehouse' );

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$stockManager = $manager->getSubManager( 'stock' );
		$this->_manager = $stockManager->getSubManager( 'warehouse' );
	}


	/**
	 * Creates a new product stock warehouse item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product warehouse properties
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
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Creates a new product stock warehouse item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "product.stock.warehouse" prefix
	 * @return MShop_Product_Item_Stock_Warehouse_Interface Product warehouse item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'product.stock.warehouse.id': $item->setId( $value ); break;
				case 'product.stock.warehouse.code': $item->setCode( $value ); break;
				case 'product.stock.warehouse.label': $item->setLabel( $value ); break;
				case 'product.stock.warehouse.status': $item->setStatus( $value ); break;
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
