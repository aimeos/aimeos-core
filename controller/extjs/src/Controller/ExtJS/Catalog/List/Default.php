<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS catalog list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Catalog_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the catalog list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Catalog_List' );
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
		$manager = $this->_getManager();
		$ids = (array) $params->items;
		$refIds = array();

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', $this->_getPrefix() . '.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item ) {
			$refIds[$item->getDomain()][] = $id;
		}

		$manager->deleteItems( $ids );

		if( isset( $refIds['product'] ) )
		{
			$this->_rebuildIndex( (array) $refIds['product'] );
			$this->_clearCache( $ids, 'product' );
		}

		return array(
			'items' => $params->items,
			'success' => true,
		);
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

		$ids = $refIds = array();
		$manager = $this->_getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->_transformValues( $entry ) );
			$manager->saveItem( $item );

			$refIds[$item->getDomain()][] = $item->getRefId();
			$ids[] = $item->getId();
		}

		if( isset( $refIds['product'] ) )
		{
			$this->_rebuildIndex( (array) $refIds['product'] );
			$this->_clearCache( $ids, 'product' );
		}

		return $this->_getItems( $ids, $this->_getPrefix() );
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
				$idLists[$domain][] = $item->getRefId();
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
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'catalog/list' );
		}

		return $this->_manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function _getPrefix()
	{
		return 'catalog.list';
	}


	/**
	 * Rebuild the index for the given product IDs
	 *
	 * @param array $prodIds List of product IDs
	 */
	protected function _rebuildIndex( array $prodIds )
	{
		$context = $this->_getContext();
		$productManager = MShop_Factory::createManager( $context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $prodIds ) );
		$search->setSlice( 0, count( $prodIds ) );

		$indexManager = MShop_Factory::createManager( $context, 'catalog/index' );
		$indexManager->rebuildIndex( $productManager->searchItems( $search ) );
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'catalog.list.datestart'} ) && $entry->{'catalog.list.datestart'} != '' ) {
			$entry->{'catalog.list.datestart'} = str_replace( 'T', ' ', $entry->{'catalog.list.datestart'} );
		} else {
			$entry->{'catalog.list.datestart'} = null;
		}

		if( isset( $entry->{'catalog.list.dateend'} ) && $entry->{'catalog.list.dateend'} != '' ) {
			$entry->{'catalog.list.dateend'} = str_replace( 'T', ' ', $entry->{'catalog.list.dateend'} );
		} else {
			$entry->{'catalog.list.dateend'} = null;
		}

		if( isset( $entry->{'catalog.list.config'} ) ) {
			$entry->{'catalog.list.config'} = (array) $entry->{'catalog.list.config'};
		}

		return $entry;
	}
}
