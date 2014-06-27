<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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

		$this->_getContext()->getCache()->deleteByTags( array( 'catalog' ) );

		return array(
			'success' => true,
		);
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
			$item = $manager->createItem();

			if( isset( $entry->{'catalog.id'} ) ) { $item->setId( $entry->{'catalog.id'} ); }
			if( isset( $entry->{'catalog.code'} ) ) { $item->setCode( $entry->{'catalog.code'} ); }
			if( isset( $entry->{'catalog.label'} ) ) { $item->setLabel( $entry->{'catalog.label'} ); }
			if( isset( $entry->{'catalog.config'} ) ) { $item->setConfig( (array) $entry->{'catalog.config'} );	}
			if( isset( $entry->{'catalog.status'} ) ) { $item->setStatus( $entry->{'catalog.status'} ); }
			$manager->insertItem( $item, $parentId, $refId );

			$ids[] = $item->getId();
		}

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

		$refId = ( isset( $params->refid ) ? $params->refid : null );
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry ) {
			$manager->moveItem( $entry, $params->oldparentid, $params->newparentid, $refId );
		}

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
			$item = $manager->createItem();

			if( isset( $entry->{'catalog.id'} ) ) { $item->setId( $entry->{'catalog.id'} ); }
			if( isset( $entry->{'catalog.code'} ) ) { $item->setCode( $entry->{'catalog.code'} ); }
			if( isset( $entry->{'catalog.label'} ) ) { $item->setLabel( $entry->{'catalog.label'} ); }
			if( isset( $entry->{'catalog.config'} ) ) {	$item->setConfig( (array) $entry->{'catalog.config'} );	}
			if( isset( $entry->{'catalog.status'} ) ) { $item->setStatus( $entry->{'catalog.status'} ); }

			$manager->saveItem( $item );

			$ids[] = $item->getId();
		}

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
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return MShop_Catalog_Manager_Factory::createManager( $this->_context );
	}
}
