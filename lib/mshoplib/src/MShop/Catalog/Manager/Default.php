<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	extends MShop_Common_Manager_ListRef_Abstract
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


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-catalog' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$search = $this->createSearch();

		$path = 'classes/catalog/manager/submanagers';
		foreach( $config->get( $path, array( 'list' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$path = 'mshop/catalog/manager/default/item/delete';
			$sql = $config->get( $path, $path );

			$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_STR );
			$translations = array( 'siteid' => '"siteid"' );

			$search->setConditions( $search->compare( '==', 'siteid', $siteids ) );
			$sql = str_replace( ':siteid', $search->getConditionString( $types, $translations ), $sql );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Creates new item object.
	 *
	 * @return MShop_Common_Item_Interface New item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );

		return $this->_createItem( $values );
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
		$this->begin();

		try
		{
			$this->_createTreeManager( $siteid )->deleteNode( $id );
			$this->commit();
		}
		catch( Exception $e )
		{
			$this->rollback();
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
		/** classes/catalog/manager/submanagers
		 * List of manager names that can be instantiated by the catalog manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'classes/catalog/manager/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array( 'list' ), $withsub );
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
		$this->begin();

		try
		{
			$this->_createTreeManager( $siteid )->insertNode( $node, $parentId, $refId );
			$this->_updateUsage( $node->getId(), $item, true );
			$this->commit();
		}
		catch( Exception $e )
		{
			$this->rollback();
			throw $e;
		}
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param mixed $id ID of the item that should be moved
	 * @param mixed $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param mixed $newParentId ID of the new parent item where the item should be moved to
	 * @param mixed $refId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$siteid = $this->_getContext()->getLocale()->getSiteId();
		$item = $this->getItem( $id );

		$this->begin();

		try
		{
			$this->_createTreeManager( $siteid )->moveNode( $id, $oldParentId, $newParentId, $refId );
			$this->_updateUsage( $id, $item );
			$this->commit();
		}
		catch( Exception $e )
		{
			$this->rollback();
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
		$this->begin();

		try
		{
			$this->_createTreeManager( $siteid )->saveNode( $node );
			$this->_updateUsage( $node->getId(), $item );
			$this->commit();
		}
		catch( Exception $e )
		{
			$this->rollback();
			throw $e;
		}
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total No function. Reference will be set to null in this case.
	 * @param integer $total
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$nodeMap = $siteMap = array();
		$context = $this->_getContext();

		$dbname = $this->_getResourceName();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;
			$cfgPathSearch = 'mshop/catalog/manager/default/item/search-item';
			$cfgPathCount = 'mshop/catalog/manager/default/item/count';
			$required = array( 'catalog' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$siteMap[$row['siteid']][$row['id']] = new MW_Tree_Node_Default( $row );
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

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this->_buildItems( $nodeMap, $ref, 'catalog' );
	}


	/**
	 * Returns a list of items starting with the given category that are in the path to the root node
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

				foreach( $path as $node ) {
					$itemMap[$node->getId()] = $node;
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

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
			$nodeid = $node->getId();

			$listItems = array();
			if( array_key_exists( $nodeid, $listItemMap ) ) {
				$listItems = $listItemMap[$nodeid];
			}

			$refItems = array();
			if( array_key_exists( $nodeid, $refItemMap ) ) {
				$refItems = $refItemMap[$nodeid];
			}

			$item = $this->_createItem( array(), $listItems, $refItems, array(), $node );
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
		/** classes/catalog/manager/name
		 * Class name of the used catalog manager implementation
		 *
		 * Each default catalog manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_Mycatalog
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/name = Mycatalog
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCatalog"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/catalog/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog manager.
		 *
		 *  mshop/catalog/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/decorators/global
		 * @see mshop/catalog/manager/decorators/local
		 */

		/** mshop/catalog/manager/decorators/global
		 * Adds a list of globally available decorators only to the catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog manager.
		 *
		 *  mshop/catalog/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/decorators/excludes
		 * @see mshop/catalog/manager/decorators/local
		 */

		/** mshop/catalog/manager/decorators/local
		 * Adds a list of local decorators only to the catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog manager.
		 *
		 *  mshop/catalog/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/decorators/excludes
		 * @see mshop/catalog/manager/decorators/global
		 */

		return $this->_getSubManager( 'catalog', $manager, $name );
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

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
		}

		foreach( $itemMap as $id => $node )
		{
			$listItems = array();
			if( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$items[$id] = $this->_createItem( array(), $listItems, $refItems, array(), $node );
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
	protected function _createItem( array $values = array(), array $listItems = array(), array $refItems = array(),
		array $children = array(), MW_Tree_Node_Interface $node = null )
	{
		if( $node === null )
		{
			if( !isset( $values['siteid'] ) ) {
				throw new MShop_Catalog_Exception( 'No site ID available for creating a catalog item' );
			}

			$node = $this->_createTreeManager( $values['siteid'] )->createNode();
			$node->siteid = $values['siteid'];
		}

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
			if( array_key_exists( $child->getId(), $listItemMap ) ) {
				$listItems = $listItemMap[$child->getId()];
			}

			$refItems = array();
			if( array_key_exists( $child->getId(), $refItemMap ) ) {
				$refItems = $refItemMap[$child->getId()];
			}

			$newItem = $this->_createItem( array(), $listItems, $refItems, array(), $child );
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


			$treeConfig = array(
				'search' => $this->_searchConfig,
				'dbname' => $this->_getResourceName(),
				'sql' => array(
					'delete' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/delete' ) ),
					'get' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/get' ) ),
					'insert' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/insert' ) ),
					'move-left' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/move-left' ) ),
					'move-right' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/move-right' ) ),
					'search' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/search' ) ),
					'update' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/update' ) ),
					'update-parentid' => str_replace( ':siteid', $siteid, $config->get( 'mshop/catalog/manager/default/item/update-parentid' ) ),
					'newid' => $config->get( 'mshop/catalog/manager/default/item/newid' ),
				),
			);

			$this->_treeManagers[$siteid] = MW_Tree_Factory::createManager( 'DBNestedSet', $treeConfig, $dbm );
		}

		return $this->_treeManagers[$siteid];
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

		$map[(string) $node->getId()] = $node;

		foreach( $node->getChildren() as $child ) {
			$map += $this->_getNodeMap( $child );
		}

		return $map;
	}


	/**
	 * Updates the usage information of a node.
	 *
	 * @param integer $id Id of the record
	 * @param MShop_Catalog_Item_Interface $item Catalog item
	 * @param boolean $case True if the record shoud be added or false for an update
	 *
	 */
	private function _updateUsage( $id, MShop_Catalog_Item_Interface $item, $case = false )
	{
		$date = date( 'Y-m-d H:i:s' );
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

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

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}
}
