<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Catalog
 */


namespace Aimeos\MShop\Catalog\Manager;


/**
 * Catalog manager with methods for managing categories products, text, media.
 *
 * @package MShop
 * @subpackage Catalog
 */
abstract class Base extends \Aimeos\MShop\Common\Manager\ListRef\Base
{
	private $searchConfig;
	private $filter = [];
	private $treeManagers = [];


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $searchConfig )
	{
		parent::__construct( $context );

		$this->searchConfig = $searchConfig;
	}


	/**
	 * Registers a new item filter for the given name
	 *
	 * To prevent catalog items to be added to the tree, you can register a
	 * closure function that checks if the item should be part of the category
	 * tree or not. The function signature must be:
	 *
	 * function( \Aimeos\MShop\Common\Item\ListRef\Iface $item, $index )
	 *
	 * It must accept an item implementing the list reference interface and the
	 * index of the category in the list starting from 0. Its return value must
	 * be a boolean value of "true" if the category item should be added to the
	 * tree and "false" if not.
	 *
	 * @param string $name Filter name
	 * @param \Closure $fcn Callback function
	 */
	public function registerItemFilter( $name, \Closure $fcn )
	{
		$this->filter[$name] = $fcn;
	}


	/**
	 * Creates the catalog item objects.
	 *
	 * @param array $itemMap Associative list of catalog ID / tree node pairs
	 * @param array $domains List of domains (e.g. text, media) whose items should be attached to the catalog items
	 * @param string $prefix Domain prefix
	 * @param array $local Associative list of IDs as keys and the associative array of items as values
	 * @return array List of items implementing \Aimeos\MShop\Catalog\Item\Iface
	 */
	protected function buildItems( array $itemMap, array $domains, $prefix, array $local = [] )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = [];

		if( count( $domains ) > 0 )
		{
			$listItems = $this->getListItems( array_keys( $itemMap ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->getRefItems( $refIdMap );
		}

		foreach( $itemMap as $id => $node )
		{
			$listItems = [];
			if( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = [];
			if( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$items[$id] = $this->createItemBase( [], $listItems, $refItems, [], $node );
		}

		return $items;
	}


	/**
	 * Creates a new catalog item.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of list items that belong to the catalog item
	 * @param array $refItems Associative list of referenced items grouped by domain
	 * @param array $children List of tree nodes implementing \Aimeos\MW\Tree\Node\Iface
	 * @param \Aimeos\MW\Tree\Node\Iface|null $node Tree node object
	 * @return \Aimeos\MShop\Catalog\Item\Iface New catalog item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [],
			array $children = [], \Aimeos\MW\Tree\Node\Iface $node = null )
	{
		if( $node === null )
		{
			if( !isset( $values['siteid'] ) ) {
				throw new \Aimeos\MShop\Catalog\Exception( 'No site ID available for creating a catalog item' );
			}

			$node = $this->createTreeManager( $values['siteid'] )->createNode();
			$node->siteid = $values['siteid'];
		}

		if( isset( $node->config ) && ( $result = json_decode( $node->config, true ) ) !== null ) {
			$node->config = $result;
		}

		return new \Aimeos\MShop\Catalog\Item\Standard( $node, $children, $listItems, $refItems );
	}


	/**
	 * Builds the tree of catalog items.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Parent tree node
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Parent tree catalog Item
	 * @param array $listItemMap Associative list of parent-item-ID / list items for the catalog item
	 * @param array $refItemMap Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function createTree( \Aimeos\MW\Tree\Node\Iface $node, \Aimeos\MShop\Catalog\Item\Iface $item,
			array $listItemMap, array $refItemMap )
	{
		foreach( $node->getChildren() as $idx => $child )
		{
			$listItems = [];
			if( array_key_exists( $child->getId(), $listItemMap ) ) {
				$listItems = $listItemMap[$child->getId()];
			}

			$refItems = [];
			if( array_key_exists( $child->getId(), $refItemMap ) ) {
				$refItems = $refItemMap[$child->getId()];
			}

			$newItem = $this->createItemBase( [], $listItems, $refItems, [], $child );

			$result = true;
			foreach( $this->filter as $fcn ) {
				$result = $result && $fcn( $newItem, $idx );
			}

			if( $result === true )
			{
				$item->addChild( $newItem );
				$this->createTree( $child, $newItem, $listItemMap, $refItemMap );
			}
		}
	}


	/**
	 * Creates an object for managing the nested set.
	 *
	 * @param integer $siteid Site ID for the specific tree
	 * @return \Aimeos\MW\Tree\Manager\Iface Tree manager
	 */
	protected function createTreeManager( $siteid )
	{
		if( !isset( $this->treeManagers[$siteid] ) )
		{
			$context = $this->getContext();
			$dbm = $context->getDatabaseManager();


			$treeConfig = array(
				'search' => $this->searchConfig,
				'dbname' => $this->getResourceName(),
				'sql' => array(

					/** mshop/catalog/manager/standard/delete/mysql
					 * Deletes the items matched by the given IDs from the database
					 *
					 * @see mshop/catalog/manager/standard/delete/ansi
					 */

					/** mshop/catalog/manager/standard/delete/ansi
					 * Deletes the items matched by the given IDs from the database
					 *
					 * Removes the records specified by the given IDs from the database.
					 * The records must be from the site that is configured via the
					 * context item.
					 *
					 * The ":cond" placeholder is replaced by the name of the ID column and
					 * the given ID or list of IDs while the site ID is bound to the question
					 * mark.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for deleting items
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'delete' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/delete' ) ),

					/** mshop/catalog/manager/standard/get/mysql
					 * Returns a node record and its complete subtree optionally limited by the level
					 *
					 * @see mshop/catalog/manager/standard/get/ansi
					 */

					/** mshop/catalog/manager/standard/get/ansi
					 * Returns a node record and its complete subtree optionally limited by the level
					 *
					 * Fetches the records matched by the given criteria from the catalog
					 * database. The records must be from one of the sites that are
					 * configured via the context item. If the current site is part of
					 * a tree of sites, the SELECT statement can retrieve all records
					 * from the current site and the complete sub-tree of sites. This
					 * statement retrieves all records that are part of the subtree for
					 * the found node. The depth can be limited by the "level" number.
					 *
					 * To limit the records matched, conditions can be added to the given
					 * criteria object. It can contain comparisons like column names that
					 * must match specific values which can be combined by AND, OR or NOT
					 * operators. The resulting string of SQL conditions replaces the
					 * ":cond" placeholder before the statement is sent to the database
					 * server.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for searching items
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'get' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/get' ) ),

					/** mshop/catalog/manager/standard/insert/mysql
					 * Inserts a new catalog node into the database table
					 *
					 * @see mshop/catalog/manager/standard/insert/ansi
					 */

					/** mshop/catalog/manager/standard/insert/ansi
					 * Inserts a new catalog node into the database table
					 *
					 * Items with no ID yet (i.e. the ID is NULL) will be created in
					 * the database and the newly created ID retrieved afterwards
					 * using the "newid" SQL statement.
					 *
					 * The SQL statement must be a string suitable for being used as
					 * prepared statement. It must include question marks for binding
					 * the values from the catalog item to the statement before they are
					 * sent to the database server. The number of question marks must
					 * be the same as the number of columns listed in the INSERT
					 * statement. The order of the columns must correspond to the
					 * order in the insertNode() method, so the correct values are
					 * bound to the columns.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for inserting records
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'insert' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/insert' ) ),

					/** mshop/catalog/manager/standard/move-left/mysql
					 * Updates the left values of the nodes that are moved within the catalog tree
					 *
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 */

					/** mshop/catalog/manager/standard/move-left/ansi
					 * Updates the left values of the nodes that are moved within the catalog tree
					 *
					 * When moving nodes or subtrees with the catalog tree, the left
					 * value of each moved node inside the nested set must be updated
					 * to match their new position within the catalog tree.
					 *
					 * The SQL statement must be a string suitable for being used as
					 * prepared statement. It must include question marks for binding
					 * the values from the catalog item to the statement before they are
					 * sent to the database server. The order of the columns must
					 * correspond to the order in the moveNode() method, so the
					 * correct values are bound to the columns.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for updating records
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'move-left' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/move-left' ) ),

					/** mshop/catalog/manager/standard/move-right/mysql
					 * Updates the left values of the nodes that are moved within the catalog tree
					 *
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 */

					/** mshop/catalog/manager/standard/move-right/ansi
					 * Updates the left values of the nodes that are moved within the catalog tree
					 *
					 * When moving nodes or subtrees with the catalog tree, the right
					 * value of each moved node inside the nested set must be updated
					 * to match their new position within the catalog tree.
					 *
					 * The SQL statement must be a string suitable for being used as
					 * prepared statement. It must include question marks for binding
					 * the values from the catalog item to the statement before they are
					 * sent to the database server. The order of the columns must
					 * correspond to the order in the moveNode() method, so the
					 * correct values are bound to the columns.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for updating records
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'move-right' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/move-right' ) ),

					/** mshop/catalog/manager/standard/search/mysql
					 * Retrieves the records matched by the given criteria in the database
					 *
					 * @see mshop/catalog/manager/standard/search/ansi
					 */

					/** mshop/catalog/manager/standard/search/ansi
					 * Retrieves the records matched by the given criteria in the database
					 *
					 * Fetches the records matched by the given criteria from the catalog
					 * database. The records must be from one of the sites that are
					 * configured via the context item. If the current site is part of
					 * a tree of sites, the SELECT statement can retrieve all records
					 * from the current site and the complete sub-tree of sites.
					 *
					 * To limit the records matched, conditions can be added to the given
					 * criteria object. It can contain comparisons like column names that
					 * must match specific values which can be combined by AND, OR or NOT
					 * operators. The resulting string of SQL conditions replaces the
					 * ":cond" placeholder before the statement is sent to the database
					 * server.
					 *
					 * If the records that are retrieved should be ordered by one or more
					 * columns, the generated string of column / sort direction pairs
					 * replaces the ":order" placeholder.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for searching items
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'search' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/search' ) ),

					/** mshop/catalog/manager/standard/update/mysql
					 * Updates an existing catalog node in the database
					 *
					 * @see mshop/catalog/manager/standard/update/ansi
					 */

					/** mshop/catalog/manager/standard/update/ansi
					 * Updates an existing catalog node in the database
					 *
					 * Items which already have an ID (i.e. the ID is not NULL) will
					 * be updated in the database.
					 *
					 * The SQL statement must be a string suitable for being used as
					 * prepared statement. It must include question marks for binding
					 * the values from the catalog item to the statement before they are
					 * sent to the database server. The order of the columns must
					 * correspond to the order in the saveNode() method, so the
					 * correct values are bound to the columns.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for updating records
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'update' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/update' ) ),

					/** mshop/catalog/manager/standard/update-parentid/mysql
					 * Updates the parent ID after moving a node record
					 *
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 */

					/** mshop/catalog/manager/standard/update-parentid/ansi
					 * Updates the parent ID after moving a node record
					 *
					 * When moving nodes with the catalog tree, the parent ID
					 * references must be updated to match the new parent.
					 *
					 * The SQL statement must be a string suitable for being used as
					 * prepared statement. It must include question marks for binding
					 * the values from the catalog item to the statement before they are
					 * sent to the database server. The order of the columns must
					 * correspond to the order in the moveNode() method, so the
					 * correct values are bound to the columns.
					 *
					 * The SQL statement should conform to the ANSI standard to be
					 * compatible with most relational database systems. This also
					 * includes using double quotes for table and column names.
					 *
					 * @param string SQL statement for updating records
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/newid/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'update-parentid' => str_replace( ':siteid', $siteid, $this->getSqlConfig( 'mshop/catalog/manager/standard/update-parentid' ) ),

					/** mshop/catalog/manager/standard/newid/mysql
					 * Retrieves the ID generated by the database when inserting a new record
					 *
					 * @see mshop/catalog/manager/standard/newid/ansi
					 */

					/** mshop/catalog/manager/standard/newid/ansi
					 * Retrieves the ID generated by the database when inserting a new record
					 *
					 * As soon as a new record is inserted into the database table,
					 * the database server generates a new and unique identifier for
					 * that record. This ID can be used for retrieving, updating and
					 * deleting that specific record from the table again.
					 *
					 * For MySQL:
					 *  SELECT LAST_INSERT_ID()
					 * For PostgreSQL:
					 *  SELECT currval('seq_mcat_id')
					 * For SQL Server:
					 *  SELECT SCOPE_IDENTITY()
					 * For Oracle:
					 *  SELECT "seq_mcat_id".CURRVAL FROM DUAL
					 *
					 * There's no way to retrive the new ID by a SQL statements that
					 * fits for most database servers as they implement their own
					 * specific way.
					 *
					 * @param string SQL statement for retrieving the last inserted record ID
					 * @since 2014.03
					 * @category Developer
					 * @see mshop/catalog/manager/standard/delete/ansi
					 * @see mshop/catalog/manager/standard/get/ansi
					 * @see mshop/catalog/manager/standard/insert/ansi
					 * @see mshop/catalog/manager/standard/update/ansi
					 * @see mshop/catalog/manager/standard/search/ansi
					 * @see mshop/catalog/manager/standard/search-item/ansi
					 * @see mshop/catalog/manager/standard/count/ansi
					 * @see mshop/catalog/manager/standard/move-left/ansi
					 * @see mshop/catalog/manager/standard/move-right/ansi
					 * @see mshop/catalog/manager/standard/update-parentid/ansi
					 * @see mshop/catalog/manager/standard/insert-usage/ansi
					 * @see mshop/catalog/manager/standard/update-usage/ansi
					 */
					'newid' => $this->getSqlConfig( 'mshop/catalog/manager/standard/newid' ),
				),
			);

			$this->treeManagers[$siteid] = \Aimeos\MW\Tree\Factory::createManager( 'DBNestedSet', $treeConfig, $dbm );
		}

		return $this->treeManagers[$siteid];
	}


	/**
	 * Creates a flat list node items.
	 *
	 * @param \Aimeos\MW\Tree\Node\Iface $node Root node
	 * @return array Associated list of ID / node object pairs
	 */
	protected function getNodeMap( \Aimeos\MW\Tree\Node\Iface $node )
	{
		$map = [];

		$map[(string) $node->getId()] = $node;

		foreach( $node->getChildren() as $child ) {
			$map += $this->getNodeMap( $child );
		}

		return $map;
	}
}
