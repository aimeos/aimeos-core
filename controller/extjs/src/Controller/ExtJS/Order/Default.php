<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the Order controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order' );

		$this->_manager = MShop_Order_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new order item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order properties
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

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Creates a new order item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "order" prefix
	 * @return MShop_Order_Item_Interface Order item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'order.id': $item->setId( $value ); break;
				case 'order.type': $item->setType( $value ); break;
				case 'order.baseid': $item->setBaseId( $value ); break;
				case 'order.relatedid': $item->setRelatedId( $value ); break;
				case 'order.statuspayment': $item->setPaymentStatus( $value ); break;
				case 'order.statusdelivery': $item->setDeliveryStatus( $value ); break;
				case 'order.datepayment':
					if( $value != '' ) {
						$item->setDatePayment( str_replace( 'T', ' ', $value ) );
					}
					break;
				case 'order.datedelivery':
					if( $value != '' ) {
						$item->setDateDelivery( str_replace( 'T', ' ', $value ) );
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
