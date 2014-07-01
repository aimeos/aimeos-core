<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS supplier controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Supplier_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the supplier controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Supplier' );

		$this->_manager = MShop_Supplier_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new supplier item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the supplier properties
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

			if( isset( $entry->{'supplier.id'} ) ) { $item->setId( $entry->{'supplier.id'} ); }
			if( isset( $entry->{'supplier.label'} ) ) { $item->setLabel( $entry->{'supplier.label'} ); }
			if( isset( $entry->{'supplier.status'} ) ) { $item->setStatus( $entry->{'supplier.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.id', $ids ) );
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
