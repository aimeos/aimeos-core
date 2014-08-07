<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS media list type controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Media_List_Type_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the media list type controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Media_List_Type' );

		$manager = MShop_Media_Manager_Factory::createManager( $context );
		$listManager = $manager->getSubManager( 'list' );
		$this->_manager = $listManager->getSubManager( 'type' );
	}


	/**
	 * Creates a new media list type item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the media list type properties
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

			if( isset( $entry->{'media.list.type.id'} ) ) { $item->setId( $entry->{'media.list.type.id'} ); }
			if( isset( $entry->{'media.list.type.code'} ) ) { $item->setCode( $entry->{'media.list.type.code'} ); }
			if( isset( $entry->{'media.list.type.domain'} ) ) { $item->setDomain( $entry->{'media.list.type.domain'} ); }
			if( isset( $entry->{'media.list.type.label'} ) ) {	$item->setLabel( $entry->{'media.list.type.label'} ); }
			if( isset( $entry->{'media.list.type.status'} ) ) { $item->setStatus( $entry->{'media.list.type.status'} ); }


			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.list.type.id', $ids ) );
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
