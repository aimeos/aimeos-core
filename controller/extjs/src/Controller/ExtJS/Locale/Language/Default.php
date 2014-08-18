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
			$item = $this->_createItem( $entry );
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
		$manager = $this->_getManager();

		$total = 0;
		$search = $manager->createSearch();

		/** controller/extjs/locale/language/default/showall
		 * Displays all available languages in lists instead of only the used ones
		 *
		 * By default, all available languages are shown in the lists displaying
		 * languages in the administration interface, e.g. in text or media panels.
		 * The content of these lists can be limited to the languages used by the
		 * locales created for a site. This can help editors to manage content
		 * faster.
		 *
		 * @param boolean True for all languages, false for only the ones used by existing locales
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		if( !$this->_getContext()->getConfig()->get( 'controller/extjs/locale/language/default/showall', true ) )
		{
			$localeManager = MShop_Locale_Manager_Factory::createManager( $this->_getContext() );

			$langids = array();
			foreach( $localeManager->searchItems( $localeManager->createSearch() ) as $item ) {
				$langids[] = $item->getLanguageId();
			}

			if( !empty( $langids ) ) {
				$search->setConditions( $search->compare( '==', 'locale.language.id', $langids ) );
			}
		}

		$search = $this->_initCriteria( $search, $params );

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
	 * Creates a new locale language item and sets the properties from the given object.
	 *
	 * @param stdClass $entry Object with public properties using the "locale.language" prefix
	 * @return MShop_Locale_Item_Language_Interface Locale language item
	 */
	protected function _createItem( stdClass $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'locale.language.id': $item->setId( $value ); break;
				case 'locale.language.code': $item->setCode( $value ); break;
				case 'locale.language.label': $item->setLabel( $value ); break;
				case 'locale.language.status': $item->setStatus( $value ); break;
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
