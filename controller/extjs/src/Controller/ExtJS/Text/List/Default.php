<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS text list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Text_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the text list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Text_List' );

		$manager = MShop_Text_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'list' );
	}


	/**
	 * Creates a new list item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the item properties
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

			if( isset( $entry->{'text.list.id'} ) ) { $item->setId( $entry->{'text.list.id'} ); }
			if( isset( $entry->{'text.list.domain'} ) ) { $item->setDomain( $entry->{'text.list.domain'} ); }
			if( isset( $entry->{'text.list.parentid'} ) ) { $item->setParentId( $entry->{'text.list.parentid'} ); }
			if( isset( $entry->{'text.list.refid'} ) ) { $item->setRefId( $entry->{'text.list.refid'} ); }
			if( isset( $entry->{'text.list.config'} ) ) { $item->setConfig( (array) $entry->{'text.list.config'} ); }
			if( isset( $entry->{'text.list.position'} ) ) { $item->setPosition( $entry->{'text.list.position'} ); }
			if( isset( $entry->{'text.list.status'} ) ) {	$item->setStatus( $entry->{'text.list.status'} ); }

			if( isset( $entry->{'text.list.typeid'} ) && $entry->{'text.list.typeid'} != '' ) {
				$item->setTypeId( $entry->{'text.list.typeid'} );
			}

			if( isset( $entry->{'text.list.datestart'} ) && $entry->{'text.list.datestart'} != '' )
			{
				$datetime = str_replace( 'T', ' ', $entry->{'text.list.datestart'} );
				$entry->{'text.list.datestart'} = $datetime;
				$item->setDateStart( $datetime );
			}

			if( isset( $entry->{'text.list.dateend'} ) && $entry->{'text.list.dateend'} != '' )
			{
				$datetime = str_replace( 'T', ' ', $entry->{'text.list.dateend'} );
				$entry->{'text.list.dateend'} = $datetime;
				$item->setDateEnd( $datetime );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.list.id', $ids ) );
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
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$totalList = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );
		$result = $this->_getManager()->searchItems( $search, array(), $totalList );

		$idLists = array();
		$listItems = array();

		foreach( $result as $item )
		{
			if( ( $domain = $item->getDomain() ) != '' ) {
				$idLists[ $domain ][] = $item->getRefId();
			}
			$listItems[] = (object) $item->toArray();
		}

		return array(
			'items' => $listItems,
			'total' => $totalList,
			'graph' => $this->_getDomainItems( $idLists ),
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
