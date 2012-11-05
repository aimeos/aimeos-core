<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Default.php
 */


/**
 * ExtJS plugin controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Plugin_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the plugin controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Plugin' );

		$this->_manager = MShop_Plugin_Manager_Factory::createManager( $context );
		$this->_context = $context;
	}


	/**
	 * Creates a new plugin item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the plugin properties
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

			if( isset( $entry->{'plugin.id'} ) ) { $item->setId( $entry->{'plugin.id'} ); }
			if( isset( $entry->{'plugin.typeid'} ) ) { $item->setTypeId( $entry->{'plugin.typeid'} ); }
			if( isset( $entry->{'plugin.label'} ) ) {$item->setLabel( $entry->{'plugin.label'} );}
			if( isset( $entry->{'plugin.provider'} ) ) { $item->setProvider( $entry->{'plugin.provider'} ); }
			if( isset( $entry->{'plugin.config'} ) ) { $item->setConfig( (array) $entry->{'plugin.config'} ); }
			if( isset( $entry->{'plugin.status'} ) ) { $item->setStatus( $entry->{'plugin.status'} ); }

			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'plugin.id', $ids ) );
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