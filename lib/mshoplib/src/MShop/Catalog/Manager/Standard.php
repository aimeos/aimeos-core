<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
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
class Standard extends Base
	implements \Aimeos\MShop\Catalog\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'id' => array(
			'code' => 'catalog.id',
			'internalcode' => 'mcat."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'catalog.siteid' => array(
			'code' => 'catalog.siteid',
			'internalcode' => 'mcat."siteid"',
			'label' => 'Site ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'parentid' => array(
			'code' => 'catalog.parentid',
			'internalcode' => 'mcat."parentid"',
			'label' => 'Parent ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'level' => array(
			'code' => 'catalog.level',
			'internalcode' => 'mcat."level"',
			'label' => 'Tree level',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'left' => array(
			'code' => 'catalog.left',
			'internalcode' => 'mcat."nleft"',
			'label' => 'Left value',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'right' => array(
			'code' => 'catalog.right',
			'internalcode' => 'mcat."nright"',
			'label' => 'Right value',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'label' => array(
			'code' => 'catalog.label',
			'internalcode' => 'mcat."label"',
			'label' => 'Label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'code' => array(
			'code' => 'catalog.code',
			'internalcode' => 'mcat."code"',
			'label' => 'Code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'catalog.target' => array(
			'code' => 'catalog.target',
			'internalcode' => 'mcat."target"',
			'label' => 'URL target',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'status' => array(
			'code' => 'catalog.status',
			'internalcode' => 'mcat."status"',
			'label' => 'Status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'config' => array(
			'code' => 'catalog.config',
			'internalcode' => 'mcat."config"',
			'label' => 'Config',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'catalog.ctime' => array(
			'label' => 'Create date/time',
			'code' => 'catalog.ctime',
			'internalcode' => 'mcat."ctime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'catalog.mtime' => array(
			'label' => 'Modify date/time',
			'code' => 'catalog.mtime',
			'internalcode' => 'mcat."mtime"',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'catalog.editor' => array(
			'code' => 'catalog.editor',
			'internalcode' => 'mcat."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'catalog.contains' => array(
			'code' => 'catalog.contains()',
			'internalcode' => '( SELECT COUNT(mcatli_cs."parentid")
				FROM "mshop_catalog_list" AS mcatli_cs
				WHERE mcat."id" = mcatli_cs."parentid" AND :site
					AND mcatli_cs."domain" = $1 AND mcatli_cs."refid" IN ( $3 ) AND mcatli_cs."typeid" = $2
					AND ( mcatli_cs."start" IS NULL OR mcatli_cs."start" <= \':date\' )
					AND ( mcatli_cs."end" IS NULL OR mcatli_cs."end" >= \':date\' ) )',
			'label' => 'Number of catalog list items, parameter(<domain>,<list type ID>,<reference IDs>)',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, $this->searchConfig );
		$this->setResourceName( 'db-catalog' );

		$date = date( 'Y-m-d H:i:00' );
		$sites = $context->getLocale()->getSitePath();

		$this->replaceSiteMarker( $this->searchConfig['catalog.contains'], 'mcatli_cs."siteid"', $sites, ':site' );
		$this->searchConfig['catalog.contains'] = str_replace( ':date', $date, $this->searchConfig['catalog.contains'] );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$search = $this->getObject()->createSearch();

		$path = 'mshop/catalog/manager/submanagers';
		foreach( $config->get( $path, array( 'lists' ) ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->cleanup( $siteids );
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/catalog/manager/standard/cleanup/mysql
			 * Deletes the categories for the given site from the database
			 *
			 * @see mshop/catalog/manager/standard/cleanup/ansi
			 */

			/** mshop/catalog/manager/standard/cleanup/ansi
			 * Deletes the categories for the given site from the database
			 *
			 * Removes the records matched by the given site ID from the catalog
			 * database.
			 *
			 * The ":siteid" placeholder is replaced by the name and value of the
			 * site ID column and the given ID or list of IDs.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for removing the records
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/catalog/manager/standard/delete/ansi
			 * @see mshop/catalog/manager/standard/insert/ansi
			 * @see mshop/catalog/manager/standard/update/ansi
			 * @see mshop/catalog/manager/standard/newid/ansi
			 * @see mshop/catalog/manager/standard/search/ansi
			 * @see mshop/catalog/manager/standard/count/ansi
			 */
			$path = 'mshop/catalog/manager/standard/cleanup';
			$sql = $this->getSqlConfig( $path );

			$types = array( 'siteid' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$translations = array( 'siteid' => '"siteid"' );

			$search->setConditions( $search->compare( '==', 'siteid', $siteids ) );
			$sql = str_replace( ':siteid', $search->getConditionSource( $types, $translations ), $sql );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, 0, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, 0x7FFFFFFF, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @return \Aimeos\MShop\Catalog\Item\Iface New catalog item object
	 */
	public function createItem( $type = null, $domain = null )
	{
		return $this->createItemBase( ['siteid' => $this->getContext()->getLocale()->getSiteId()] );
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Returns the Search object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'catalog' );
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
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$this->begin();

		try
		{
			$this->createTreeManager( $siteid )->deleteNode( $id );
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->rollback();

			if( $e->getCode() == 40001 ) {
				$this->deleteItem( $id );
			} else {
				throw $e;
			}
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
			$this->getObject()->deleteItem( $id );
		}
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param boolean $default True to add default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = null, $type = null, $default = false )
	{
		return $this->findItemBase( array( 'catalog.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the item specified by its ID.
	 *
	 * @param integer $id Unique ID of the catalog item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Catalog\Item\Iface Returns the catalog item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'catalog.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/catalog/manager/submanagers';

		return $this->getResourceTypeBase( 'catalog', $path, array( 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/catalog/manager/submanagers
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
		$path = 'mshop/catalog/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'lists' ), $withsub );
	}


	/**
	 * Adds a new item object.
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Item which should be inserted
	 * @param string|null $parentId ID of the parent item where the item should be inserted into
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 * @return \Aimeos\MShop\Catalog\Item\Iface $item Updated item including the generated ID
	 */
	public function insertItem( \Aimeos\MShop\Catalog\Item\Iface $item, $parentId = null, $refId = null )
	{
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$node = $item->getNode();
		$this->begin();

		try
		{
			$this->createTreeManager( $siteid )->insertNode( $node, $parentId, $refId );
			$this->updateUsage( $node->getId(), $item, true );
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->rollback();

			if( $e->getCode() == 40001 ) {
				$this->insertItem( $item, $parentId, $refId );
			} else {
				throw $e;
			}
		}

		$item = $this->saveListItems( $item, 'catalog' );
		return $this->saveChildren( $item );
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param string $id ID of the item that should be moved
	 * @param string $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param string $newParentId ID of the new parent item where the item should be moved to
	 * @param string|null $refId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$item = $this->getObject()->getItem( $id );

		$this->begin();

		try
		{
			$this->createTreeManager( $siteid )->moveNode( $id, $oldParentId, $newParentId, $refId );
			$this->updateUsage( $id, $item );
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->rollback();

			if( $e->getCode() == 40001 ) {
				$this->moveItem( $id, $oldParentId, $newParentId, $refId );
			} else {
				throw $e;
			}
		}
	}


	/**
	 * Updates an item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		self::checkClass( '\\Aimeos\\MShop\\Catalog\\Item\\Iface', $item );

		if( !$item->isModified() )
		{
			$item = $this->saveListItems( $item, 'catalog', $fetch );
			return $this->saveChildren( $item );
		}

		$siteid = $this->getContext()->getLocale()->getSiteId();
		$node = $item->getNode();
		$this->begin();

		try
		{
			$this->createTreeManager( $siteid )->saveNode( $node );
			$this->updateUsage( $node->getId(), $item );
			$this->commit();
		}
		catch( \Exception $e )
		{
			$this->rollback();

			if( $e->getCode() == 40001 ) {
				$this->saveItem( $item, $fetch );
			} else {
				throw $e;
			}
		}

		$item = $this->saveListItems( $item, 'catalog', $fetch );
		return $this->saveChildren( $item );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$nodeMap = $siteMap = [];
		$context = $this->getContext();

		$dbname = $this->getResourceName();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'catalog' );

			/** mshop/catalog/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole catalog domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/standard/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param integer Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.01
			 * @see mshop/locale/manager/standard/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;
			$level = $context->getConfig()->get( 'mshop/catalog/manager/sitemode', $level );

			/** mshop/catalog/manager/standard/search-item/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/catalog/manager/standard/search-item/ansi
			 */

			/** mshop/catalog/manager/standard/search-item/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the catalog
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
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
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
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
			 * @see mshop/catalog/manager/standard/search/ansi
			 * @see mshop/catalog/manager/standard/count/ansi
			 * @see mshop/catalog/manager/standard/move-left/ansi
			 * @see mshop/catalog/manager/standard/move-right/ansi
			 * @see mshop/catalog/manager/standard/update-parentid/ansi
			 */
			$cfgPathSearch = 'mshop/catalog/manager/standard/search-item';

			/** mshop/catalog/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/catalog/manager/standard/count/ansi
			 */

			/** mshop/catalog/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the catalog
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/catalog/manager/standard/delete/ansi
			 * @see mshop/catalog/manager/standard/get/ansi
			 * @see mshop/catalog/manager/standard/insert/ansi
			 * @see mshop/catalog/manager/standard/update/ansi
			 * @see mshop/catalog/manager/standard/newid/ansi
			 * @see mshop/catalog/manager/standard/search/ansi
			 * @see mshop/catalog/manager/standard/search-item/ansi
			 * @see mshop/catalog/manager/standard/move-left/ansi
			 * @see mshop/catalog/manager/standard/move-right/ansi
			 * @see mshop/catalog/manager/standard/update-parentid/ansi
			 */
			$cfgPathCount = 'mshop/catalog/manager/standard/count';

			if( $search->getSortations() === [] ) {
				$search->setSortations( [$search->sort( '+', 'catalog.left')] );
			}

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$siteMap[$row['siteid']][$row['id']] = new \Aimeos\MW\Tree\Node\Standard( $row );
			}

			$sitePath = array_reverse( $this->getContext()->getLocale()->getSitePath() );

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
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this->buildItems( $nodeMap, $ref, 'catalog' );
	}


	/**
	 * Returns a list of items starting with the given category that are in the path to the root node
	 *
	 * @param integer $id ID of item to get the path for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing \Aimeos\MShop\Catalog\Item\Iface with IDs as keys
	 */
	public function getPath( $id, array $ref = [] )
	{
		$sitePath = array_reverse( $this->getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$path = $this->createTreeManager( $siteId )->getPath( $id );
			} catch( \Exception $e ) {
				continue;
			}

			if( !empty( $path ) )
			{
				$itemMap = [];

				foreach( $path as $node ) {
					$itemMap[$node->getId()] = $node;
				}

				return $this->buildItems( $itemMap, $ref, 'catalog' );
			}
		}

		throw new \Aimeos\MShop\Catalog\Exception( sprintf( 'Catalog path for ID "%1$s" not found', $id ) );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param string|null $id Retrieve nodes starting from the given ID
	 * @param string[] List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from \Aimeos\MW\Tree\Manager\Base
	 * @param \Aimeos\MW\Criteria\Iface|null $criteria Optional criteria object with conditions
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item, maybe with subnodes
	 */
	public function getTree( $id = null, array $ref = [], $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, \Aimeos\MW\Criteria\Iface $criteria = null )
	{
		$sitePath = array_reverse( $this->getContext()->getLocale()->getSitePath() );

		foreach( $sitePath as $siteId )
		{
			try {
				$node = $this->createTreeManager( $siteId )->getNode( $id, $level, $criteria );
			} catch( \Exception $e ) {
				continue;
			}

			$listItems = $listItemMap = $refIdMap = [];
			$nodeMap = $this->getNodeMap( $node );

			if( count( $ref ) > 0 ) {
				$listItems = $this->getListItems( array_keys( $nodeMap ), $ref, 'catalog' );
			}

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->getRefItems( $refIdMap );
			$nodeid = $node->getId();

			$listItems = [];
			if( array_key_exists( $nodeid, $listItemMap ) ) {
				$listItems = $listItemMap[$nodeid];
			}

			$refItems = [];
			if( array_key_exists( $nodeid, $refItemMap ) ) {
				$refItems = $refItemMap[$nodeid];
			}

			$item = $this->createItemBase( [], $listItems, $refItems, [], $node );
			$this->createTree( $node, $item, $listItemMap, $refItemMap );

			return $item;
		}

		throw new \Aimeos\MShop\Catalog\Exception( sprintf( 'No catalog node for ID "%1$s"', $id ) );
	}


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $manager Name of the sub manager type
	 * @param string|null $name Name of the implementation, will be from configuration (or Default)
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'catalog', $manager, $name );
	}


	/**
	 * Saves the children of the given node
	 *
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Catalog item object incl. child items
	 * @return \Aimeos\MShop\Catalog\Item\Iface Catalog item with saved child items
	 */
	protected function saveChildren( \Aimeos\MShop\Catalog\Item\Iface $item )
	{
		$rmIds = [];
		foreach( $item->getChildrenDeleted() as $child ) {
			$rmIds[] = $child->getId();
		}

		$this->deleteItems( $rmIds );

		foreach( $item->getChildren() as $child )
		{
			if( $child->getId() !== null )
			{
				$this->saveItem( $child );

				if( $child->getParentId() !== $item->getParentId() ) {
					$this->moveItem( $child->getId(), $item->getParentId(), $child->getParentId() );
				}
			}
			else
			{
				$this->insertItem( $child, $item->getId() );
			}
		}

		return $item;
	}


	/**
	 * Updates the usage information of a node.
	 *
	 * @param integer $id Id of the record
	 * @param \Aimeos\MShop\Catalog\Item\Iface $item Catalog item
	 * @param boolean $case True if the record shoud be added or false for an update
	 *
	 */
	private function updateUsage( $id, \Aimeos\MShop\Catalog\Item\Iface $item, $case = false )
	{
		$date = date( 'Y-m-d H:i:s' );
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$siteid = $context->getLocale()->getSiteId();

			if( $case !== true )
			{
				/** mshop/catalog/manager/standard/update-usage/mysql
				 * Updates the config, editor and mtime value of an updated record
				 *
				 * @see mshop/catalog/manager/standard/update-usage/ansi
				 */

				/** mshop/catalog/manager/standard/update-usage/ansi
				 * Updates the config, editor and mtime value of an updated record
				 *
				 * Each record contains some usage information like when it was
				 * created, last modified and by whom. These information are part
				 * of the catalog items and the generic tree manager doesn't care
				 * about this information. Thus, they are updated after the tree
				 * manager saved the basic record information.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the catalog item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the method using this statement,
				 * so the correct values are bound to the columns.
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
				 */
				$path = 'mshop/catalog/manager/standard/update-usage';
			}
			else
			{
				/** mshop/catalog/manager/standard/insert-usage/mysql
				 * Updates the config, editor, ctime and mtime value of an inserted record
				 *
				 * @see mshop/catalog/manager/standard/insert-usage/ansi
				 */

				/** mshop/catalog/manager/standard/insert-usage/ansi
				 * Updates the config, editor, ctime and mtime value of an inserted record
				 *
				 * Each record contains some usage information like when it was
				 * created, last modified and by whom. These information are part
				 * of the catalog items and the generic tree manager doesn't care
				 * about this information. Thus, they are updated after the tree
				 * manager inserted the basic record information.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the catalog item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the method using this statement,
				 * so the correct values are bound to the columns.
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
				 * @see mshop/catalog/manager/standard/update-usage/ansi
				 */
				$path = 'mshop/catalog/manager/standard/insert-usage';
			}

			$stmt = $conn->create( $this->getSqlConfig( $path ) );
			$stmt->bind( 1, json_encode( $item->getConfig() ) );
			$stmt->bind( 2, $date ); // mtime
			$stmt->bind( 3, $context->getEditor() );
			$stmt->bind( 4, $item->getTarget() );

			if( $case !== true )
			{
				$stmt->bind( 5, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 6, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}
			else
			{
				$stmt->bind( 5, $date ); // ctime
				$stmt->bind( 6, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 7, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}
}
