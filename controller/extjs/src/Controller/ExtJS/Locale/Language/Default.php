<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS language controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Locale_Language_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the language controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Locale_Language' );

		$this->_manager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'language' );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param stdClass $params Associative list of parameters
	 */
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items' ) );

		foreach( (array) $params->items as $id ) {
			$this->_getManager()->deleteItem( $id );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Creates a new language item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items' ) );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if( isset( $entry->{'locale.language.id'} ) ) { $item->setId( $entry->{'locale.language.id'} ); }
			if( isset( $entry->{'locale.language.code'} ) ) { $item->setCode( $entry->{'locale.language.code'} ); }
			if( isset( $entry->{'locale.language.label'} ) ) { $item->setLabel( $entry->{'locale.language.label'} ); }
			if( isset( $entry->{'locale.language.status'} ) ) { $item->setStatus( $entry->{'locale.language.status'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.language.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( stdClass $params )
	{
		$total = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );

		$sort = $search->getSortations();
		$sort[] = $search->sort( '+', 'locale.language.label' );
		$search->setSortations( $sort );

		$items = $this->_getManager()->searchItems( $search, array(), $total );

		return array(
			'items' => $this->_toArray( $items ),
			'total' => $total,
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			'Locale_Language.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.saveItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.searchItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "condition","optional" => true ),
					array( "type" => "integer","name" => "start","optional" => true ),
					array( "type" => "integer","name" => "limit","optional" => true ),
					array( "type" => "string","name" => "sort","optional" => true ),
					array( "type" => "string","name" => "dir","optional" => true ),
				),
				"returns" => "array",
			),
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
