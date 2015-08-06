<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS site controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Locale_Site_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the site controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Locale_Site' );
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
	 * Creates a new site item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the product properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items' ) );

		$ids = array();
		$manager = $this->_getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->_transformValues( $entry ) );
			$manager->saveItem( $item );
			$ids[] = $item->getId();
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
		$total = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );

		$sort = $search->getSortations();
		$sort[] = $search->sort( '+', 'locale.site.left' );
		$search->setSortations( $sort );

		$items = $this->_getManager()->searchItems( $search, array(), $total );

		return array(
			'items' => $this->_toArray( $items ),
			'total' => $total,
			'success' => true,
		);
	}


	/**
	 * Inserts a new item or a list of new items depending on the parent and the referenced item ID.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with nodes and success value
	 */
	public function insertItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items' ) );

		$manager = $this->_getManager();

		$refId = ( isset( $params->refid ) ? $params->refid : null );
		$parentId = ( ( isset( $params->parentid ) && $params->parentid !== 'root' ) ? $params->parentid : null );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->_transformValues( $entry ) );
			$manager->insertItem( $item, $parentId, $refId );

			$entry->{'locale.site.id'} = $item->getId();
		}

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Moves an item or a list of items depending on the old parent, the new parent and the referenced item ID.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function moveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items', 'oldparentid', 'newparentid', 'refid' ) );

		$manager = $this->_getManager();

		if( $params->newparentid === 'root' ) {
			$params->newparentid = null;
		}

		$refId = ( isset( $params->refid ) ? $params->refid : null );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $id ) {
			$manager->moveItem( $id, $params->oldparentid, $params->newparentid, $refId );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Returns an item or a list of items including their children for the given IDs.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with nodes and success value
	 */
	public function getTree( stdClass $params )
	{
		$this->_checkParams( $params, array( 'items' ) );

		$manager = $this->_getManager();

		$result = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			if( $entry == 'root' )
			{
				$search = $this->_manager->createSearch();
				$search->setConditions( $search->compare( '==', 'locale.site.level', 0 ) );

				$item = $this->_manager->createItem();
				$item->setLabel( 'Root' );

				foreach( $this->_manager->searchItems( $search ) as $siteItem ) {
					$item->addChild( $siteItem );
				}
			}
			else
			{
				$item = $manager->getTree( $entry, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );
			}

			$result[] = $this->_createNodeArray( $item );
		}

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $result ) : $result ),
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
			'Locale_Site.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.saveItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.searchItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "condition","optional" => true ),
					array( "type" => "integer","name" => "start","optional" => true ),
					array( "type" => "integer","name" => "limit","optional" => true ),
					array( "type" => "string","name" => "sort","optional" => true ),
					array( "type" => "string","name" => "dir","optional" => true ),
					array( "type" => "array","name" => "options","optional" => true ),
				),
				"returns" => "array",
			),
			'Locale_Site.getTree' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Site.insertItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "string","name" => "parentid","optional" => true ),
					array( "type" => "string","name" => "refid","optional" => true ),
				),
				"returns" => "array",
			),
			'Locale_Site.moveItems' => array(
				"parameters" => array(
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "string","name" => "oldparentid","optional" => false ),
					array( "type" => "string","name" => "newparentid","optional" => false ),
					array( "type" => "string","name" => "refid","optional" => true ),
				),
				"returns" => "array",
			),
		);
	}


	/**
	 * Creates a list of items with children.
	 *
	 * @param MShop_Locale_Item_Site_Interface $item Locale site item
	 */
	protected function _createNodeArray( MShop_Locale_Item_Site_Interface $item )
	{
		$result = $item->toArray();

		if( method_exists( $item, 'getChildren' ) )
		{
			foreach( $item->getChildren() as $child ) {
				$result['children'][] = $this->_createNodeArray( $child );
			}
		}

		return (object) $result;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'locale/site' );
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
		return 'locale.site';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'locale.site.config'} ) ) {
			$entry->{'locale.site.config'} = (array) $entry->{'locale.site.config'};
		}

		return $entry;
	}
}
