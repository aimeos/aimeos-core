<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Default implementation for managing sites.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Manager_Site_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Locale_Manager_Site_Interface
{
	private $_cache = array();
	private $_config;
	private $_dbm;

	private $_searchConfig = array(
		'locale.site.id' => array(
			'code' => 'locale.site.id',
			'internalcode' => 'mlocsi."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."id")' ),
			'label' => 'Locale site ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'locale.site.siteid' => array(
			'code' => 'locale.site.siteid',
			'internalcode' => 'mlocsi."id"',
			'label' => 'Locale site ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'locale.site.code' => array(
			'code' => 'locale.site.code',
			'internalcode' => 'mlocsi."code"',
			'label' => 'Locale site code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.site.label' => array(
			'code' => 'locale.site.label',
			'internalcode' => 'mlocsi."label"',
			'label' => 'Locale site label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.site.config' => array(
			'code' => 'locale.site.config',
			'internalcode' => 'mlocsi."config"',
			'label' => 'Locale site config',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.site.status' => array(
			'code' => 'locale.site.status',
			'internalcode' => 'mlocsi."status"',
			'label' => 'Locale site status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'locale.site.ctime'=> array(
			'code'=>'locale.site.ctime',
			'internalcode'=>'mlocsi."ctime"',
			'label'=>'Locale site create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.site.mtime'=> array(
			'code'=>'locale.site.mtime',
			'internalcode'=>'mlocsi."mtime"',
			'label'=>'Locale site modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.site.editor'=> array(
			'code'=>'locale.site.editor',
			'internalcode'=>'mlocsi."editor"',
			'label'=>'Locale site editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'level' => array(
			'code'=>'locale.site.level',
			'internalcode'=>'mlocsi."level"',
			'label'=>'Locale site tree level',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'left' => array(
			'code'=>'locale.site.left',
			'internalcode'=>'mlocsi."nleft"',
			'label'=>'Locale site left value',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'right' => array(
			'code'=>'locale.site.right',
			'internalcode'=>'mlocsi."nright"',
			'label'=>'Locale site right value',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Creates a manager for the locale site.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct($context);

		$this->_config = $context->getConfig();
		$this->_dbm = $context->getDatabaseManager();
	}


	/**
	 * Creates a new site object.
	 *
	 * @return MShop_Locale_Item_Site_Interface
	 * @throws MShop_Locale_Exception
	 */
	public function createItem()
	{
		return $this->_createItem();
	}


	/**
	 * Adds a new site to the storage or updates an existing one.
	 *
	 * @param MShop_Common_Item_Interface $site New site item  for saving to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Locale_Exception
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Locale_Item_Site_Interface';
		if ( !( $item instanceof $iface ) ) {
			throw new MShop_Locale_Exception(sprintf('Object is not of required type "%1$s"', $iface));
		}

		if( $item->getId() === null ) {
			throw new MShop_Locale_Exception( sprintf( 'Newly created site can not be saved using method "saveItem()". Try using method "insertItem()" instead.' ) );
		}

		if( !$item->isModified() ) { return	; }

		$conn = $this->_dbm->acquire();
		$context = $this->_getContext();
		$config = $context->getConfig();

		try
		{
			$id = $item->getId();

			$path = 'mshop/locale/manager/site/default/item/update';
			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(2, $item->getLabel(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(3, json_encode($item->getConfig()), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(5, $context->getEditor() );
			$stmt->bind(6, date( 'Y-m-d H:i:s', time() ) ); // mtime
			$stmt->bind(7, $id, MW_DB_Statement_Abstract::PARAM_INT);

			$stmt->execute()->finish();
			$item->setId( $id ); // set Modified false

			$this->_dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$this->_dbm->release($conn);
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
		$path = 'mshop/locale/manager/site/default/item/delete';
		$this->_deleteItems($ids, $this->_getContext()->getConfig()->get( $path, $path ), false );
	}


	/**
	 * Returns the site item specified by its ID.
	 *
	 * @param string $siteId Site id to create the Site object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Locale_Item_Site_Interface Returns the site item of the given id
	 * @throws MShop_Locale_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'locale.site.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array( );

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if ( $withsub === true ) {
			foreach ( $this->_getContext()->getConfig()->get('classes/locale/manager/site/submanagers', array( )) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Locale_Manager_Interface manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'locale', 'site/' . $manager, $name );
	}


	/**
	 * Searches for site items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of site items implementing MShop_Locale_Item_Site_Interface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Locale_Exception On failures with the site item object
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$conn = $this->_dbm->acquire();
		$items = array();

		try
		{
			$attributes = $this->getSearchAttributes();
			$types = $this->_getSearchTypes($attributes);
			$translations = $this->_getSearchTranslations($attributes);

			$find = array( ':cond', ':order', ':start', ':size' );
			$replace = array(
				$search->getConditionString($types, $translations),
				$search->getSortationString($types, $translations),
				$search->getSliceStart(),
				$search->getSliceSize(),
			);

			$path = 'mshop/locale/manager/site/default/item/search';
			$sql = $this->_config->get($path, $path);
			$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

			try
			{
				while ( ($row = $results->fetch()) !== false )
				{
					$config = $row['config'];
					if ( ( $row['config'] = json_decode( $row['config'], true ) ) === null ) {
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale.config', $row['id'], $config );
						$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
					}

					$items[ $row['id'] ] = $this->_createItem($row);
				}
			} catch ( Exception $e ) {
				$results->finish();
				throw $e;
			}

			if ( $total !== null )
			{
				$path = 'mshop/locale/manager/site/default/item/count';
				$sql = $this->_getContext()->getConfig()->get($path, $path);
				$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Locale_Exception('No total results value found');
				}

				$total = $row['count'];
			}

			$this->_dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$this->_dbm->release($conn);
			throw $e;
		}
		return $items;
	}


	/**
	 * Creates a search object and sets base criteria.
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if ( $default === true ) {
			$search = parent::_createSearch('locale.site');
		} else {
			$search = parent::createSearch();
		}

		$expr = array(
			$search->compare( '==', 'locale.site.level', 0 ),
			$search->getConditions(),
		);

		$search->setConditions( $search->combine( '&&', $expr ) );

		return $search;
	}


	/**
	 * Returns a list of item IDs, that are in the path of given item ID.
	 *
	 * @param integer $id ID of item to get the path for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return array Associative list of items implementing MShop_Locale_Item_Site_Interface with IDs as keys
	 */
	public function getPath( $id, array $ref = array() )
	{
		$item = $this->getTree( $id, $ref, MW_Tree_Manager_Abstract::LEVEL_ONE );
		return array( $item->getId() => $item );
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param array List of domains (e.g. text, media, etc.) whose referenced items should be attached to the objects
	 * @param integer $level One of the level constants from MW_Tree_Manager_Abstract
	 * @return MShop_Locale_Item_Site_Interface Site item
	 */
	public function getTree( $id = null, array $ref = array(), $level = MW_Tree_Manager_Abstract::LEVEL_TREE )
	{
		if( $id !== null )
		{
			if( count( $ref ) > 0 ) {
				return $this->getItem( $id, $ref );
			}

			if( !isset( $this->_cache[$id] ) ) {
				$this->_cache[$id] = $this->getItem( $id, $ref );
			}

			return $this->_cache[$id];
		}

		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'locale.site.code', 'default' ) );
		$criteria->setSlice( 0, 1 );

		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MShop_Locale_Exception( sprintf( 'Tree root with code "%1$s" in "%2$s" not found', 'default', 'locale.site.code' ) );
		}

		$this->_cache[ $item->getId() ] = $item;

		return $item;
	}


	/**
	 * Adds a new item object.
	 *
	 * @param MShop_Locale_Item_Site_Interface $item Item which should be inserted
	 * @param integer $parentId ID of the parent item where the item should be inserted into
	 * @param integer $refId ID of the item where the item should be inserted before (null to append)
	 */
	public function insertItem( MShop_Locale_Item_Site_Interface $item, $parentId = null, $refId = null )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$curdate = date( 'Y-m-d H:i:s' );

			$path = 'mshop/locale/manager/site/default/item/insert';
			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(2, $item->getLabel(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(3, json_encode($item->getConfig()), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(5, 0, MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(6, $context->getEditor() );
			$stmt->bind(7, $curdate ); // mtime
			$stmt->bind(8, $curdate ); // ctime

			$stmt->execute()->finish();

			$path = 'mshop/locale/manager/default/item/newid';
			$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
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
		throw new MShop_Locale_Exception( sprintf( 'Method "%1$s" for locale site manager not available', 'moveItem()' ) );
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param $sql SQL statement
	 * @return MW_DB_Result_Interface Search result object
	 */
	protected function _getSearchResults( MW_DB_Connection_Interface $conn, $sql )
	{
		$statement = $conn->create($sql);
		$this->_getContext()->getLogger()->log(__METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG);

		$results = $statement->execute();

		return $results;
	}


	/**
	 * Create new item object initialized with given parameters.
	 *
	 * @return MShop_Locale_Item_Site_Interface
	 */
	protected function _createItem( array $data = array( ) )
	{
		return new MShop_Locale_Item_Site_Default($data);
	}


	/**
	 * Returns the raw search config array.
	 *
	 * @return array List of search config arrays
	 */
	protected function _getSearchConfig()
	{
		return $this->_searchConfig;
	}
}
