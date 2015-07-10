<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Product_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the Order base product controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base_Product' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$baseManager =  $manager->getSubManager( 'base' );
		$this->_manager = $baseManager->getSubManager( 'product' );
	}


	/**
	 * Creates a new order base product item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base product properties
	 * @return array Associative list with nodes and success value
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
		$search->setConditions( $search->compare( '==', 'order.base.product.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Creates a new order base product item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "order.base.product" prefix
	 * @return MShop_Order_Item_Base_Product_Interface Order product item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'order.base.product.id': $item->setId( $value ); break;
				case 'order.base.product.type': $item->setType( $value ); break;
				case 'order.base.product.baseid': $item->setBaseId( $value ); break;
				case 'order.base.product.orderproductid': $item->setOrderProductId( $value ); break;
				case 'order.base.product.suppliercode': $item->setSupplierCode( $value ); break;
				case 'order.base.product.prodcode': $item->setProductCode( $value ); break;
				case 'order.base.product.quantity': $item->setQuantity( $value ); break;
				case 'order.base.product.name': $item->setName( $value ); break;
				case 'order.base.product.flags': $item->setFlags( $value ); break;
				case 'order.base.product.status': $item->setStatus( $value ); break;
				case 'order.base.product.position': $item->setPosition( $value ); break;
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
