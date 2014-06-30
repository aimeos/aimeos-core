<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Service_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the service controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Service' );

		$this->_manager = MShop_Service_Manager_Factory::createManager( $context );
		$this->_context = $context;
	}


	/**
	 * Creates a new service item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the service properties
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

			if( isset( $entry->{'service.id'} ) ) { $item->setId( $entry->{'service.id'} ); }
			if( isset( $entry->{'service.position'} ) ) { $item->setPosition( $entry->{'service.position'} ); }
			if( isset( $entry->{'service.typeid'} ) ) { $item->setTypeId( $entry->{'service.typeid'} ); }
			if( isset( $entry->{'service.code'} ) ) { $item->setCode( $entry->{'service.code'} ); }
			if( isset( $entry->{'service.label'} ) ) { $item->setLabel( $entry->{'service.label'} ); }
			if( isset( $entry->{'service.provider'} ) ) { $item->setProvider( $entry->{'service.provider'} ); }
			if( isset( $entry->{'service.config'} ) ) { $item->setConfig( (array) $entry->{'service.config'} ); }
			if( isset( $entry->{'service.status'} ) ) { $item->setStatus( $entry->{'service.status'} ); }

			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.id', $ids ) );
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