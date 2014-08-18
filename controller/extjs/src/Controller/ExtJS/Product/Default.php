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
			$item = $this->_createItem( (array) $entry );
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

		$ids = (array) $params->items;
		$context = $this->_getContext();
		$manager = $this->_getManager();


		$manager->deleteItems( $ids );


		foreach( array( 'catalog', 'product' ) as $domain )
		{
			$manager = MShop_Factory::createManager( $context, $domain . '/list' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain.'.list.refid', $ids ),
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
	 * Creates a new product item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "product" prefix
	 * @return MShop_Product_Item_Interface Product item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_manager->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'product.id': $item->setId( $value ); break;
				case 'product.code': $item->setCode( $value ); break;
				case 'product.label': $item->setLabel( $value ); break;
				case 'product.typeid': $item->setTypeId( $value ); break;
				case 'product.status': $item->setStatus( $value ); break;
				case 'product.suppliercode': $item->setSupplierCode( $value ); break;
				case 'product.datestart':
					if( $value != '' )
					{
						$value = str_replace( 'T', ' ', $value );
						$entry->{'product.datestart'} = $value;
						$item->setDateStart( $value );
					}
					break;
				case 'product.dateend':
					if( $value != '' )
					{
						$value = str_replace( 'T', ' ', $value );
						$entry->{'product.dateend'} = $value;
						$item->setDateEnd( $value );
					}
					break;
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
