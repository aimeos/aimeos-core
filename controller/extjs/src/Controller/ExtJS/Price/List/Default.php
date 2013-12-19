<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS price list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Price_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the price list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Price_List' );

		$manager = MShop_Price_Manager_Factory::createManager( $context );
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

			if( isset( $entry->{'price.list.id'} ) ) { $item->setId( $entry->{'price.list.id'} ); }
			if( isset( $entry->{'price.list.domain'} ) ) { $item->setDomain( $entry->{'price.list.domain'} ); }
			if( isset( $entry->{'price.list.parentid'} ) ) { $item->setParentId( $entry->{'price.list.parentid'} ); }
			if( isset( $entry->{'price.list.refid'} ) ) { $item->setRefId( $entry->{'price.list.refid'} ); }
			if( isset( $entry->{'price.list.config'} ) ) { $item->setConfig( (array) $entry->{'price.list.config'} ); }
			if( isset( $entry->{'price.list.position'} ) ) { $item->setPosition( $entry->{'price.list.position'} ); }
			if( isset( $entry->{'price.list.status'} ) ) { $item->setStatus( $entry->{'price.list.status'} ); }

			if( isset( $entry->{'price.list.typeid'} ) && $entry->{'price.list.typeid'} != '' ) {
				$item->setTypeId( $entry->{'price.list.typeid'} );
			}

			if( isset( $entry->{'price.list.datestart'} ) && $entry->{'price.list.datestart'} != '' )
			{
				$datetime = str_replace( 'T', ' ', $entry->{'price.list.datestart'} );
				$entry->{'price.list.datestart'} = $datetime;
				$item->setDateStart( $datetime );
			}

			if( isset( $entry->{'price.list.dateend'} ) && $entry->{'price.list.dateend'} != '' )
			{
				$datetime = str_replace( 'T', ' ', $entry->{'price.list.dateend'} );
				$entry->{'price.list.dateend'} = $datetime;
				$item->setDateEnd( $datetime );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'price.list.id', $ids ) );
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
