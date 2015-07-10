<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS catalog controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Catalog_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;
	private $_context = null;


	/**
	 * Initializes the RPC catalog controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Catalog' );

		$this->_manager = MShop_Catalog_Manager_Factory::createManager( $context );
		$this->_context = $context;
	}


	/**
	 * Returns a node or a list of nodes including their children for the given IDs.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with nodes and success value
	 */
	public function getTree( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$manager = $this->_getManager();

		$result = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$entry = ( $entry != 'root' ? $entry : null );
			$item = $manager->getTree( $entry, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );
			$result[] = $this->_createNodeArray( $item );
		}

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $result ) : $result ),
			'success' => true,
		);
	}


	/**
	 * Inserts a new node or a list of new nodes depending on the parent and the referenced node ID.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with nodes and success value
	 */
	public function insertItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$manager = $this->_getManager();

		$refId = ( isset( $params->refid ) ? $params->refid : null );
		$parentId = ( isset( $params->parentid ) ? $params->parentid : null );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$manager->insertItem( $item, $parentId, $refId );

			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Moves a node or a list of nodes depending on the old parent, the new parent and the referenced node ID.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function moveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items', 'oldparentid', 'newparentid' ) );
		$this->_setLocale( $params->site );

		$manager = $this->_getManager();

		$ids = array();
		$refId = ( isset( $params->refid ) ? $params->refid : null );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$manager->moveItem( $entry, $params->oldparentid, $params->newparentid, $refId );
			$ids[] = $entry->id;
		}

		$this->_clearCache( $ids );

		return array(
			'success' => true,
		);
	}


	/**
	 * Updates an existing node or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the node properties
	 * @return array Associative list with nodes and success value
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$manager = $this->_getManager();

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_createItem( (array) $entry );
			$manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->_clearCache( $ids );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
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
		$desc = parent::getServiceDescription();

		$catdesc = array(
			'Catalog.getTree' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
				),
				"returns" => "array",
			),
			'Catalog.insertItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "string","name" => "parentid","optional" => true ),
					array( "type" => "string","name" => "refid","optional" => true ),
				),
				"returns" => "array",
			),
			'Catalog.moveItems' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "string","name" => "oldparentid","optional" => false ),
					array( "type" => "string","name" => "newparentid","optional" => false ),
					array( "type" => "string","name" => "refid","optional" => true ),
				),
				"returns" => "array",
			),
		);

		return array_merge($desc, $catdesc);
	}


	/**
	 * Creates a new catalog item and sets the properties from the given array.
	 *
	 * @param array $entry Associative list of name and value properties using the "catalog" prefix
	 * @return MShop_Catalog_Item_Interface Catalog item
	 */
	protected function _createItem( array $entry )
	{
		$item = $this->_getManager()->createItem();

		foreach( $entry as $name => $value )
		{
			switch( $name )
			{
				case 'catalog.id': $item->setId( $value ); break;
				case 'catalog.code': $item->setCode( $value ); break;
				case 'catalog.label': $item->setLabel( $value ); break;
				case 'catalog.domain': $item->setDomain( $value ); break;
				case 'catalog.status': $item->setStatus( $value ); break;
				case 'catalog.config': $item->setConfig( (array) $value ); break;
			}
		}

		return $item;
	}


	/**
	 * Creates a list of nodes with children.
	 *
	 * @param MShop_Catalog_Item_Interface $node Catalog node
	 */
	protected function _createNodeArray( MShop_Catalog_Item_Interface $node )
	{
		$result = $node->toArray();

		foreach( $node->getChildren() as $child ) {
			$result['children'][] = $this->_createNodeArray( $child );
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
		return $this->_manager;
	}
}
