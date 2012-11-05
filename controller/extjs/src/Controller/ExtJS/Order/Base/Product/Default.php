<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Default.php 14265 2011-12-11 16:57:33Z nsendetzky $
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
			$item = $this->_manager->createItem();

			if( isset( $entry->{'order.base.product.id'} ) ) { $item->setId( $entry->{'order.base.product.id'} ); }
			if( isset( $entry->{'order.base.product.baseid'} ) ) { $item->setBaseId( $entry->{'order.base.product.baseid'} ); }
			if( isset( $entry->{'order.base.product.orderproductid'} ) ) { $item->setOrderProductId( $entry->{'order.base.product.orderproductid'} ); }
			if( isset( $entry->{'order.base.product.type'} ) ) { $item->setType( $entry->{'order.base.product.type'} ); }
			if( isset( $entry->{'order.base.product.suppliercode'} ) ) { $item->setSupplierCode( $entry->{'order.base.product.suppliercode'} ); }
			if( isset( $entry->{'order.base.product.prodcode'} ) ) { $item->setProductCode( $entry->{'order.base.product.prodcode'} ); }
			if( isset( $entry->{'order.base.product.name'} ) ) { $item->setName( $entry->{'order.base.product.name'} ); }
			if( isset( $entry->{'order.base.product.quantity'} ) ) { $item->setQuantity( $entry->{'order.base.product.quantity'} ); }
			if( isset( $entry->{'order.base.product.flags'} ) ) { $item->setFlags( $entry->{'order.base.product.flags'} ); }
			if( isset( $entry->{'order.base.product.status'} ) ) { $item->setStatus( $entry->{'order.base.product.status'} ); }

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
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
