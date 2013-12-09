<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the product list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product_List' );

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$this->_manager = $manager->getSubManager( 'list' );
		$this->_context = $context;
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

			if( isset( $entry->{'product.list.id'} ) ) { $item->setId( $entry->{'product.list.id'} ); }
			if( isset( $entry->{'product.list.domain'} ) ) { $item->setDomain( $entry->{'product.list.domain'} ); }
			if( isset( $entry->{'product.list.parentid'} ) ) { $item->setParentId( $entry->{'product.list.parentid'} ); }
			if( isset( $entry->{'product.list.refid'} ) ) { $item->setRefId( $entry->{'product.list.refid'} ); }
			if( isset( $entry->{'product.list.position'} ) ) { $item->setPosition( $entry->{'product.list.position'} ); }
			if( isset( $entry->{'product.list.status'} ) ) { $item->setStatus( $entry->{'product.list.status'} );	}

			if( isset( $entry->{'product.list.typeid'} ) && $entry->{'product.list.typeid'} != '' ) {
				$item->setTypeId( $entry->{'product.list.typeid'} );
			}

			if( isset( $entry->{'product.list.datestart'} ) && $entry->{'product.list.datestart'} != '' )
			{
				$entry->{'product.list.datestart'} = $entry->{'product.list.datestart'};
				$item->setDateStart( $entry->{'product.list.datestart'} );
			}

			if( isset( $entry->{'product.list.dateend'} ) && $entry->{'product.list.dateend'} != '' )
			{
				$entry->{'product.list.dateend'} = $entry->{'product.list.dateend'};
				$item->setDateEnd( $entry->{'product.list.dateend'} );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.list.id', $ids ) );
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
