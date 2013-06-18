<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS price type controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Price_Type_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the price type controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Price_Type' );

		$manager = MShop_Price_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'type' );
	}


	/**
	 * Creates a new price type item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product properties
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

			if( isset( $entry->{'price.type.id'} ) ) { $item->setId( $entry->{'price.type.id'} ); }
			if( isset( $entry->{'price.type.code'} ) ) { $item->setCode( $entry->{'price.type.code'} ); }
			if( isset( $entry->{'price.type.domain'} ) ) { $item->setDomain( $entry->{'price.type.domain'} ); }
			if( isset( $entry->{'price.type.label'} ) ) { $item->setLabel( $entry->{'price.type.label'} ); }
			if( isset( $entry->{'price.type.status'} ) ) { $item->setStatus( $entry->{'price.type.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.type.id', $ids ) );
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
