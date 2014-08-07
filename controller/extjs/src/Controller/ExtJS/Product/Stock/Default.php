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
			$item = $this->_manager->createItem();

			if( isset( $entry->{'product.stock.id'} ) ) { $item->setId( $entry->{'product.stock.id'} ); }
			if( isset( $entry->{'product.stock.productid'} ) ) { $item->setProductId( $entry->{'product.stock.productid'} ); }

			if( isset( $entry->{'product.stock.warehouseid'} ) && $entry->{'product.stock.warehouseid'} !== '' ) {
				$item->setWarehouseId( $entry->{'product.stock.warehouseid'} );
			}

			if( isset( $entry->{'product.stock.stocklevel'} ) && $entry->{'product.stock.stocklevel'} !== '' ) {
				$item->setStocklevel( $entry->{'product.stock.stocklevel'} );
			}

			if( isset( $entry->{'product.stock.dateback'} ) && $entry->{'product.stock.dateback'} !== '' )
			{
				$datetime = str_replace( 'T', ' ', $entry->{'product.stock.dateback'} );
				$entry->{'product.stock.dateback'} = $datetime;
				$item->setDateBack( $datetime );
			}

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
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
