<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS locale controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Locale_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the locale controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Locale' );

		$this->_manager = MShop_Locale_Manager_Factory::createManager( $context );
	}


	/**
	 * Creates a new locale item or updates an existing one or a list thereof.
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

			if( isset( $entry->{'locale.id'} ) ) { $item->setId( $entry->{'locale.id'} ); }
			if( isset( $entry->{'locale.languageid'} ) ) { $item->setLanguageId( $entry->{'locale.languageid'} ); }
			if( isset( $entry->{'locale.currencyid'} ) ) { $item->setCurrencyId( $entry->{'locale.currencyid'} ); }
			if( isset( $entry->{'locale.status'} ) ) { $item->setStatus( $entry->{'locale.status'} ); }
			if( isset( $entry->{'locale.siteid'} ) ) {	$item->setSiteId( $entry->{'locale.siteid'} ); }
			if( isset( $entry->{'locale.position'} ) ) { $item->setPosition( $entry->{'locale.position'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.id', $ids ) );
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
