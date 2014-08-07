<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS order base service attribute controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Service_Attribute_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the order base service attribute controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base_Service_Attribute' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$baseManager = $manager->getSubManager( 'base' );
		$serviceManager = $baseManager->getSubManager( 'service' );
		$this->_manager = $serviceManager->getSubManager( 'attribute' );
	}


	/**
	 * Creates a new order base service attribute item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base service attribute properties
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

			if( isset( $entry->{'order.base.service.attribute.id'} ) ) { $item->setId( $entry->{'order.base.service.attribute.id'} ); }
			if( isset( $entry->{'order.base.service.attribute.ordservid'} ) ) { $item->setServiceId( $entry->{'order.base.service.attribute.ordservid'} ); }
			if( isset( $entry->{'order.base.service.attribute.name'} ) ) { $item->setName( $entry->{'order.base.service.attribute.name'} ); }
			if( isset( $entry->{'order.base.service.attribute.code'} ) ) { $item->setCode( $entry->{'order.base.service.attribute.code'} ); }
			if( isset( $entry->{'order.base.service.attribute.value'} ) ) { $item->setValue( $entry->{'order.base.service.attribute.value'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.attribute.id', $ids ) );
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
