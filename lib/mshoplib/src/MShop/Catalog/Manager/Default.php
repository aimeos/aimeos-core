<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Catalog manager with methods for managing categories products, text, media.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Interface, MShop_Common_Manager_Factory_Interface
{
	private $_treeManagers = array();

	private $_searchConfig = array(
		'id' => array(
			'code'=>'catalog.id',
			'internalcode'=>'mcat."id"',
			'label'=>'Catalog node ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'label' => array(
			'code'=>'catalog.label',
			'internalcode'=>'mcat."label"',
			'label'=>'Catalog node label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'config' => array(
			'code' => 'catalog.config',
			'internalcode' => 'mcat."config"',
			'label' => 'Catalog site config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'code' => array(
			'code'=>'catalog.code',
			'internalcode'=>'mcat."code"',
			'label'=>'Catalog node code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'status' => array(
			'code'=>'catalog.status',
			'internalcode'=>'mcat."status"',
			'label'=>'Catalog node status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'parentid' => array(
			'code'=>'catalog.parentid',
			'internalcode'=>'mcat."parentid"',
			'label'=>'Catalog node parentid',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'level' => array(
			'code'=>'catalog.level',
			'internalcode'=>'mcat."level"',
			'label'=>'Catalog node tree level',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'left' => array(
			'code'=>'catalog.left',
			'internalcode'=>'mcat."nleft"',
			'label'=>'Catalog node left value',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'right' => array(
			'code'=>'catalog.right',
			'internalcode'=>'mcat."nright"',
			'label'=>'Catalog node right value',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.siteid' => array(
			'code'=>'catalog.siteid',
			'internalcode'=>'mcat."siteid"',
			'label'=>'Catalog node site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.ctime'=> array(
			'label' => 'Catalog creation time',
			'code' => 'catalog.ctime',
			'internalcode' => 'mcat."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.mtime'=> array(
			'label' => 'Catalog modification time',
			'code' => 'catalog.mtime',
			'internalcode' => 'mcat."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.editor'=> array(
			'code'=>'catalog.editor',
			'internalcode'=>'mcat."editor"',
			'label'=>'Catalog editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'catalog.list.id'=> array(
			'code'=>'catalog.list.id',
			'internalcode'=>'mcatli."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_catalog_list" AS mcatli ON ( mcat."id" = mcatli."parentid" )' ),
			'label'=>'Catalog list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.siteid'=> array(
			'code'=>'catalog.list.siteid',
			'internalcode'=>'mcatli."siteid"',
			'label'=>'Catalog list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.parentid'=> array(
			'code'=>'catalog.list.parentid',
			'internalcode'=>'mcatli."parentid"',
			'label'=>'Catalog list parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.domain'=> array(
			'code'=>'catalog.list.domain',
			'internalcode'=>'mcatli."domain"',
			'label'=>'Catalog list Domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.typeid'=> array(
			'code'=>'catalog.list.typeid',
			'internalcode'=>'mcatli."typeid"',
			'label'=>'Catalog list type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.refid'=> array(
			'code'=>'catalog.list.refid',
			'internalcode'=>'mcatli."refid"',
			'label'=>'Catalog list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.datestart' => array(
			'code'=>'catalog.list.datestart',
			'internalcode'=>'mcatli."start"',
			'label'=>'Catalog list start date',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.dateend' => array(
			'code'=>'catalog.list.dateend',
			'internalcode'=>'mcatli."end"',
			'label'=>'Catalog list end date',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.position' => array(
			'code'=>'catalog.list.position',
			'internalcode'=>'mcatli."pos"',
			'label'=>'Catalog list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.status' => array(
			'code'=>'catalog.list.status',
			'internalcode'=>'mcatli."status"',
			'label'=>'Catalog list status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.ctime'=> array(
			'label' => 'Catalog list creation time',
			'code' => 'catalog.list.ctime',
			'internalcode' => 'mcatli."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.mtime'=> array(
			'label' => 'Catalog list modification time',
			'code' => 'catalog.list.mtime',
			'internalcode' => 'mcatli."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.editor'=> array(
			'code'=>'catalog.list.editor',
			'internalcode'=>'mcatli."editor"',
			'label'=>'Catalog list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listTypeSearchConfig = array(
		'catalog.list.type.id' => array(
			'code'=>'catalog.list.type.id',
			'internalcode'=>'mcatlity."id"',
			'internaldeps'=>array('LEFT JOIN "mshop_catalog_list_type" as mcatlity ON ( mcatli."typeid" = mcatlity."id" )'),
			'label'=>'Catalog list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.siteid' => array(
			'code'=>'catalog.list.type.siteid',
			'internalcode'=>'mcatlity."siteid"',
			'label'=>'Catalog list type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.list.type.code' => array(
			'code'=>'catalog.list.type.code',
			'internalcode'=>'mcatlity."code"',
			'label'=>'Catalog list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.domain' => array(
			'code'=>'catalog.list.type.domain',
			'internalcode'=>'mcatlity."domain"',
			'label'=>'Catalog list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.label' => array(
			'code' => 'catalog.list.type.label',
			'internalcode' => 'mcatlity."label"',
			'label' => 'Catalog list type label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.status' => array(
			'code' => 'catalog.list.type.status',
			'internalcode' => 'mcatlity."status"',
			'label' => 'Catalog list type status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'catalog.list.type.ctime'=> array(
			'label' => 'Catalog list type creation time',
			'code' => 'catalog.list.type.ctime',
			'internalcode' => 'mcatlity."ctime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.mtime'=> array(
			'label' => 'Catalog list type modification time',
			'code' => 'catalog.list.type.mtime',
			'internalcode' => 'mcatlity."mtime"',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'catalog.list.type.editor'=> array(
			'code'=>'catalog.list.type.editor',
			'internalcode'=>'mcatlity."editor"',
			'label'=>'Catalog list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates new item object.
	 *
	 * @return MShop_Common_Item_Interface New item object
	 */
	public function createItem()
	{
		$siteid = $this->_getContext()->getLocale()->getSiteId();

		$node = $this->_createTreeManager( $siteid )->createNode();
		$node->siteid = $siteid;

		return $this->_createItem( $node );
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Returns the Search object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->_createSearch( 'catalog' );
		}

		return parent::createSearch();
	}


	/**
	 * Deletes the item specified by its ID.
	 *
	 * @param mixed $id ID of the item object
	 */
	public function deleteItem( $id )
	{
		$siteid = $this->_getContext()->getLocale()->getSiteId();
		$this->_begin();

		try
		{
			$this->_createTreeManager( $siteid )->deleteNode( $id );
			$this->_commit();
		}
		catch( Exception $e )
		{
			$this->_rollback();
			throw $e;
		}
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		foreach( $ids as $id ) {
			$this->deleteItem( $id );
		}
	}


	/**
	 * Returns the item specified by its ID.
	 *
	 * @param integer $id Unique ID of the catalog item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Catalog_Item_Interface Returns the catalog item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'catalog.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/catalog/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array( 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Adds a new item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item which should be inserted
	 */
	public function insertItem( MShop_Catalog_Item_Interface $item, $parentId = null, $refId = null )
	{
		$siteid = $this->_getContext()->getLocale()->getSiteId();
		$node = $item->getNode();
		$this->_begin();

		try
		{
			$this->_createTreeManager( $siteid )->insertNode( $node, $parentId, $refId );
			$this->_updateUsage( $node->getId(), $item, true );
			$this->_commit();
		}
		catch( Exception $e )
		{
			$this->_rollback();
			throw $e;
		}
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param mixed $id ID of the item that should be moved
	 * @param mixed $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param mixed $newParentId ID of the new parent item where the item should be moved to
	 * @param mixed $newRefId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$siteid = $this->_getContext()->getLocale()->getSiteId();
		$item = $this->getItem( $id );

		$this->_begin();

		try
		{
			$this->_createTreeManager( $siteid )->moveNode( $id, $oldParentId, $newParentId, $refId );
			$this->_updateUsage( $id, $item );
			$this->_commit();
		}
		catch( Exception $e )
		{
			$this->_rollback();
			throw $e;
		}
	}


	/**
	 * Updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Catalog_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Catalog_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$siteid = $this->_getContext()->getLocale()->getSiteId();
		$node = $item->getNode();
		$this->_begin();

		try
		{
			$this->_createTreeManager( $siteid )->saveNode( $node );
			$this->_updateUsage( $node->getId(), $item );
			$this->_commit();
		}
		catch( Exception $e )
		{
			$this->_rollback();
			throw $e;
		}
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total No function. Reference will be set to null in this case.
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$nodeMap = $siteMap = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;
			$cfgPathSearch = 'mshop/catalog/manager/default/item/search-item';
			$cfgPathCount = 'mshop/catalog/manager/default/item/count';
			$required = array( 'catalog' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$siteMap[ $row['siteid'] ][ $row['id'] ] = new MW_Tree_Node_Default( $row );
			}

			$sitePath = array_reverse( $this->_getContext()->getLocale()->getSitePath() );

			foreach( $sitePath as $siteId )
			{
				if( isset( $siteMap[$siteId] ) && !empty( $siteMap[$siteId] ) )
				{
					$nodeMap = $siteMap[$siteId];
					break;
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $this->_buildItems( $nodeMap, $ref, 'catalog' );
	}


	/**
	 * Returns a list if item IDs, that are in the path of given item ID.
	 *
	 * @param integer $id ID of item to get the path for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing MShop_Catalog_Item_Interface with IDs as keys
	 */
	public function getPath( $id, array $ref = array() )
	{
		$sitePath = array_reverse( $this->_getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$path = $this->_createTreeManager( $siteId )->getPath( $id );
			} catch( Exception $e ) {
				continue;
			}

			if( !empty( $path ) )
			{
				$itemMap = array();

				foreach ( $path as $node ) {
					$itemMap[ $node->getId() ] = $node;
				}

				return $this->_buildItems( $itemMap, $ref, 'catalog' );
			}
		}

		throw new MShop_Catalog_Exception( sprintf( 'Catalog path for ID "%1$s" not found', $id ) );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param array List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from MW_Tree_Manager_Abstract
	 * @param MW_Common_Criteria_Interface|null $criteria Optional criteria object with conditions
	 * @return MShop_Catalog_Item_Interface Catalog item, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = array(), $level = MW_Tree_Manager_Abstract::LEVEL_TREE, MW_Common_Criteria_Interface $criteria = null )
	{
		$sitePath = array_reverse( $this->_getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$node = $this->_createTreeManager( $siteId )->getNode( $id, $level, $criteria );
			} catch( Exception $e ) {
				continue;
			}

			$listItems = $listItemMap = $refIdMap = array();
			$nodeMap = $this->_getNodeMap( $node );

			if( count( $ref ) > 0 ) {
				$listItems = $this->_getListItems( array_keys( $nodeMap ), $ref, 'catalog' );
			}

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[ $parentid ][ $domain ][ $listItem->getId() ] = $listItem;
				$refIdMap[ $domain ][ $listItem->getRefId() ][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
			$nodeid = $node->getId();

			$listItems = array();
			if ( array_key_exists( $nodeid, $listItemMap ) ) {
				$listItems = $listItemMap[ $nodeid ];
			}

			$refItems = array();
			if ( array_key_exists( $nodeid, $refItemMap ) ) {
				$refItems = $refItemMap[ $nodeid ];
			}

			$item = $this->_createItem( $node, array(), $listItems, $refItems );
			$this->_createTree( $node, $item, $listItemMap, $refItemMap );

			return $item;
		}

		throw new MShop_Catalog_Exception( sprintf( 'Catalog node for ID "%1$s" not available', $id ) );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $manager Name of the sub manager type
	 * @param string $name Name of the implementation, will be from configuration (or Default)
	 * @return MShop_Common_Manager_Interface Manager extending the domain functionality
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'list':
				$typeManager = $this->_getTypeManager( 'catalog', 'list/type', null, $this->_getListTypeSearchConfig() );
				return $this->_getListManager( 'catalog', $manager, $name, $this->_getListSearchConfig(), $typeManager );
			default:
				return $this->_getSubManager( 'catalog', $manager, $name );
		}
	}


	/**
	 * Creates the catalog item objects.
	 *
	 * @param array $itemMap Associative list of catalog ID / tree node pairs
	 * @param array $domains List of domains (e.g. text, media) whose items should be attached to the catalog items
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Catalog_Item_Interface
	 */
	protected function _buildItems( array $itemMap, array $domains, $prefix )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = array();

		if( count( $domains ) > 0 )
		{
			$listItems = $this->_getListItems( array_keys( $itemMap ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[ $parentid ][ $domain ][ $listItem->getId() ] = $listItem;
				$refIdMap[ $domain ][ $listItem->getRefId() ][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
		}

		foreach ( $itemMap as $id => $node )
		{
			$listItems = array();
			if ( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if ( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$items[ $id ] = $this->_createItem( $node, array(), $listItems, $refItems );
		}

		return $items;
	}


	/**
	 * Creates a new catalog item.
	 *
	 * @param MW_Tree_Node_Interface $node Nested set tree node
	 * @param array $children List of children of this catalog item
	 * @param array $listItems List of list items that belong to the catalog item
	 * @param array $refItems Associative list of referenced items grouped by domain
	 * @return MShop_Catalog_Item_Interface New catalog item
	 */
	protected function _createItem( MW_Tree_Node_Interface $node = null, array $children = array(),
		array $listItems = array(), array $refItems = array() )
	{
		if( isset( $node->config ) && ( $result = json_decode( $node->config, true ) ) !== null ) {
			$node->config = $result;
		}

		return new MShop_Catalog_Item_Default( $node, $children, $listItems, $refItems );
	}


	/**
	 * Builds the tree of catalog items.
	 *
	 * @param MW_Tree_Node_Interface $node Parent tree node
	 * @param MShop_Catalog_Item_Interface $item Parent tree catalog Item
	 * @param array $listItemMap Associative list of parent-item-ID / list items for the catalog item
	 * @param array $refItemMap Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function _createTree( MW_Tree_Node_Interface $node, MShop_Catalog_Item_Interface $item,
		array $listItemMap, array $refItemMap )
	{
		foreach( $node->getChildren() as $child )
		{
			$listItems = array();
			if ( array_key_exists( $child->getId(), $listItemMap ) ) {
				$listItems = $listItemMap[ $child->getId() ];
			}

			$refItems = array();
			if ( array_key_exists( $child->getId(), $refItemMap ) ) {
				$refItems = $refItemMap[ $child->getId() ];
			}

			$newItem = $this->_createItem( $child, array(), $listItems, $refItems );
			$item->addChild( $newItem );

			$this->_createTree( $child, $newItem, $listItemMap, $refItemMap );
		}
	}


	/**
	 * Creates an object for managing the nested set.
	 *
	 * @param integer $siteid Site ID for the specific tree
	 * @return MW_Tree_Manager_Interface Tree manager
	 */
	protected function _createTreeManager( $siteid )
	{
		if( !isset( $this->_treeManagers[$siteid] ) )
		{
			$context = $this->_getContext();
			$config = $context->getConfig();
			$dbm = $context->getDatabaseManager();

			$treeConfig['search'] = $this->_searchConfig;
			$treeConfig['sql'] = array(
				'delete' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/delete' ) ),
				'get' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/get' ) ),
				'insert' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/insert' ) ),
				'move-left' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/move-left' ) ),
				'move-right' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/move-right' ) ),
				'search' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/search' ) ),
				'update' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/update' ) ),
				'update-parentid' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/update-parentid' ) ),
				'newid' => $config->get( 'mshop/catalog/manager/default/item/newid' ),
			);

			$this->_treeManagers[$siteid] = MW_Tree_Factory::createManager( 'DBNestedSet', $treeConfig, $dbm );
		}

		return $this->_treeManagers[$siteid];
	}


	/**
	 * Returns the list search config array.
	 *
	 * @return array List of associative arrays which contains the search config
	 */
	protected function _getListSearchConfig()
	{
		return $this->_listSearchConfig;
	}


	/**
	 * Returns the list type search config array.
	 *
	 * @return array List of associative arrays which contains the search config
	 */
	protected function _getListTypeSearchConfig()
	{
		return $this->_listTypeSearchConfig;
	}


	/**
	 * Creates a flat list node items.
	 *
	 * @param MW_Tree_Node_Interface $node Root node
	 * @return Associated list of ID / node object pairs
	 */
	protected function _getNodeMap( MW_Tree_Node_Interface $node )
	{
		$map = array();

		$map[ (string) $node->getId() ] = $node;

		foreach( $node->getChildren() as $child ) {
			$map += $this->_getNodeMap( $child );
		}

		return $map;
	}


	/**
	 * Updates the usage information of a node.
	 *
	 * @param integer $id Id of the record
	 * @param MShop_Common_Item_Interface $item Catalog item
	 * @param boolean $case True if the record shoud be added or false for an update
	 *
	 */
	private function _updateUsage( $id, MShop_Common_Item_Interface $item, $case = false )
	{
		$date = date( 'Y-m-d H:i:s' );
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$siteid = $context->getLocale()->getSiteId();

			if( $case !== true ) {
				$path = 'mshop/catalog/manager/default/item/usage/update';
			} else {
				$path = 'mshop/catalog/manager/default/item/usage/add';
			}

			$stmt = $conn->create( $context->getConfig()->get( $path, $path ) );
			$stmt->bind( 1, json_encode( $item->getConfig() ) );
			$stmt->bind( 2, $date ); // mtime
			$stmt->bind( 3, $context->getEditor() );

			if( $case !== true )
			{
				$stmt->bind( 4, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 5, $id, MW_DB_Statement_Abstract::PARAM_INT );
			}
			else
			{
				$stmt->bind( 4, $date ); // ctime
				$stmt->bind( 5, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 6, $id, MW_DB_Statement_Abstract::PARAM_INT );
			}

			$result = $stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}
}
