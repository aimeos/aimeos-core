<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Product_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the product controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Product' );

		$this->_manager = MShop_Product_Manager_Factory::createManager( $context );
		$this->_context = $context;
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function finish( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $params->items ) );
		$search->setSlice( 0, count( $params->items ) );

		foreach( $this->_manager->searchItems( $search ) as $item ) {
			$indexManager->saveItem( $item );
		}

		$this->_clearCache( (array) $params->items );

		return array(
			'success' => true,
		);
	}


	/**
	 * Creates a new product item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product properties
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

			if( isset( $entry->{'product.id'} ) ) { $item->setId( $entry->{'product.id'} ); }
			if( isset( $entry->{'product.typeid'} ) ) { $item->setTypeId( $entry->{'product.typeid'} ); }
			if( isset( $entry->{'product.code'} ) ) { $item->setCode( $entry->{'product.code'} ); }
			if( isset( $entry->{'product.label'} ) ) { $item->setLabel( $entry->{'product.label'} ); }
			if( isset( $entry->{'product.status'} ) ) { $item->setStatus( $entry->{'product.status'} ); }
			if( isset( $entry->{'product.suppliercode'} ) ) { $item->setSupplierCode( $entry->{'product.suppliercode'} ); }

			if( isset( $entry->{'product.datestart'} ) && $entry->{'product.datestart'} != '' )
			{
				$entry->{'product.datestart'} = $entry->{'product.datestart'};
				$item->setDateStart( $entry->{'product.datestart'} );
			}

			if( isset( $entry->{'product.dateend'} ) && $entry->{'product.dateend'} != '' )
			{
				$entry->{'product.dateend'} = $entry->{'product.dateend'};
				$item->setDateEnd( $entry->{'product.dateend'} );
			}

			$this->_manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	* Deletes an item or a list of items.
	*
	* @param stdClass $params Associative list of parameters
	* @return array Associative list with success value
	*/
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$manager->deleteItems( $ids );


		foreach( array( 'catalog', 'product' ) as $domain )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain.'.list.refid', $domainIds ),
				$search->compare( '==', $domain.'.list.domain', 'product' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain.'.list.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}


		$this->_clearCache( $ids );

		return array(
				'items' => $params->items,
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
