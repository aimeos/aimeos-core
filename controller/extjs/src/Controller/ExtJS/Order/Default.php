<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
			$item = $this->_manager->createItem();

			if( isset( $entry->{'order.id'} ) ) { $item->setId( $entry->{'order.id'} ); }
			if( isset( $entry->{'order.baseid'} ) ) { $item->setBaseId( $entry->{'order.baseid'} ); }
			if( isset( $entry->{'order.type'} ) ) { $item->setType( $entry->{'order.type'} ); }

			if( isset( $entry->{'order.datepayment'} ) && $entry->{'order.datepayment'} != '' )
			{
				$entry->{'order.datepayment'} = $entry->{'order.datepayment'};
				$item->setDatePayment( $entry->{'order.datepayment'} );
			}

			if( isset( $entry->{'order.datedelivery'} ) && $entry->{'order.datedelivery'} != '' )
			{
				$entry->{'order.datedelivery'} = $entry->{'order.datedelivery'};
				$item->setDateDelivery( $entry->{'order.datedelivery'} );
			}

			if( isset( $entry->{'order.statusdelivery'} ) ) { $item->setDeliveryStatus( $entry->{'order.statusdelivery'} ); }
			if( isset( $entry->{'order.statuspayment'} ) ) { $item->setPaymentStatus( $entry->{'order.statuspayment'} ); }
			if( isset( $entry->{'order.relatedid'} ) ) { $item->setRelatedId( $entry->{'order.relatedid'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

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
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
