<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Default locale manager implementation.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Manager_Default
	extends MShop_Locale_Manager_Abstract
	implements MShop_Locale_Manager_Interface
{
	private $_searchConfig = array(
		'locale.id' => array(
			'code' => 'locale.id',
			'internalcode' => 'mloc."id"',
			'label' => 'Locale ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'locale.siteid' => array(
			'code' => 'locale.siteid',
			'internalcode' => 'mloc."siteid"',
			'label' => 'Locale site',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'locale.languageid' => array(
			'code' => 'locale.languageid',
			'internalcode' => 'mloc."langid"',
			'label' => 'Locale language ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.currencyid' => array(
			'code' => 'locale.currencyid',
			'internalcode' => 'mloc."currencyid"',
			'label' => 'Locale currency ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.position' => array(
			'code' => 'locale.position',
			'internalcode' => 'mloc."pos"',
			'label' => 'Locale position',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'locale.status' => array(
			'code' => 'locale.status',
			'internalcode' => 'mloc."status"',
			'label' => 'Locale status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'locale.ctime'=> array(
			'code'=>'locale.ctime',
			'internalcode'=>'mloc."ctime"',
			'label'=>'Locale create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.mtime'=> array(
			'code'=>'locale.mtime',
			'internalcode'=>'mloc."mtime"',
			'label'=>'Locale modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.editor'=> array(
			'code'=>'locale.editor',
			'internalcode'=>'mloc."editor"',
			'label'=>'Locale editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
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
		$this->_setResourceName( 'db-locale' );
	}


	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code (optional)
	 * @param string $currency Currency code (optional)
	 * @param boolean $active Flag to get only active items (optional)
	 * @param integer|null $level Constant from abstract class which site ID levels should be available (optional),
	 * 	based on config or value for SITE_PATH if null
	 * @return MShop_Locale_Item_Interface Locale item for the given parameters
	 * @throws MShop_Locale_Exception If no locale item is found
	 */
	public function bootstrap( $site, $lang = '', $currency = '', $active = true, $level = null )
	{
		$siteManager = $this->getSubManager( 'site' );
		$siteSearch = $siteManager->createSearch();
		$siteSearch->setConditions( $siteSearch->compare( '==', 'locale.site.code', $site ) );
		$siteItems = $siteManager->searchItems( $siteSearch );

		if( ( $siteItem = reset( $siteItems ) ) === false ) {
			throw new MShop_Locale_Exception( sprintf( 'Site for code "%1$s" not found', $site ) );
		}

		$siteIds = array( $siteItem->getId() );

		return $this->_bootstrap( $site, $lang, $currency, $active, $siteItem, $siteIds, $siteIds );
	}


	/**
	 * Creates a new locale item object.
	 *
	 * @return MShop_Locale_Item_Interface
	 */
	public function createItem()
	{
		try {
			return $this->_createItem( array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() ) );
		} catch ( Exception $e ) {
			return $this->_createItem();
		}
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
			return parent::_createSearch('locale');
		}

		return parent::createSearch();
	}


	/**
	 * Returns the item specified by its ID.
	 *
	 * @param integer $id Unique ID of the locale item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Locale_Item_Interface Returns the locale item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'locale.id', $id, $ref );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$locale = $this->_getContext()->getLocale();
		$siteIds = $locale->getSitePath();
		$siteIds[] = $locale->getSiteId();
		$items = array();

		$search = clone $search;
		$expr = array(
			$search->compare( '==', 'locale.siteid', $siteIds ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $this->_search( $search, $ref, $total ) as $row ) {
			$items[ $row['id'] ] = $this->_createItem( $row );
		}

		return $items;
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/locale/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Locale_Item_Interface';
		if ( !( $item instanceof $iface ) ) {
			throw new MShop_Locale_Exception(sprintf('Object is not of required type "%1$s"', $iface));
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/locale/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $item->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $item->getLanguageId(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(3, $item->getCurrencyId(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(4, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(5, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(6, date( 'Y-m-d H:i:s', time() ) ); // mtime
			$stmt->bind(7, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind(8, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(8, date( 'Y-m-d H:i:s', time() ) ); // ctime
			}

			$stmt->execute()->finish();

			if ( $fetch === true )
			{
				if ( $id === null ) {
					$path = 'mshop/locale/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId($id); //so item is no longer modified
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns a new manager for locale extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g site, language, currency.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager('locale', $manager, $name);
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
			$path = 'classes/locale/manager/submanagers';
			foreach ( $this->_getContext()->getConfig()->get($path, array( 'language', 'currency', 'site' )) as $sub ) {
				$list = array_merge($list, $this->getSubManager($sub)->getSearchAttributes());
			}
		}

		return $list;
	}


	/**
	 * Returns the locale item for the given site code, language code and currency code.
	 *
	 * If the locale item is inherited from a parent site, the site ID of this locale item
	 * is changed to the site ID of the actual site. This ensures that items assigned to
	 * the same site as the site item are still used.
	 *
	 * @param string $site Site code
	 * @param string $lang Language code
	 * @param string $currency Currency code
	 * @param boolean $active Flag to get only active items
	 * @param MShop_Locale_Item_Site_Interface Site item
	 * @param array $sitePath List of site IDs up to the root site
	 * @param array $siteSubTree List of site IDs below and including the current site
	 * @return MShop_Locale_Item_Interface Locale item for the given parameters
	 * @throws MShop_Locale_Exception If no locale item is found
	 */
	protected function _bootstrap( $site, $lang, $currency, $active,
		MShop_Locale_Item_Site_Interface $siteItem, array $sitePath, array $siteSubTree )
	{
		$siteId = $siteItem->getId();

		// Try to find exact match
		$search = $this->createSearch( $active );

		$expr = array( $search->compare( '==', 'locale.siteid', $sitePath ) );

		if( !empty( $lang ) ) {
			$expr[] = $search->compare( '==', 'locale.languageid', $lang );
		}

		if( !empty( $currency ) ) {
			$expr[] = $search->compare( '==', 'locale.currencyid', $currency );
		}

		$expr[] = $search->getConditions();


		if ( $active === true )
		{
			$expr[] = $search->compare( '>', 'locale.currency.status', 0 );
			$expr[] = $search->compare( '>', 'locale.language.status', 0 );
			$expr[] = $search->compare( '>', 'locale.site.status', 0 );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_search( $search );

		// Try to find first item where site matches
		foreach( $result as $row )
		{
			if( $row['siteid'] == $siteId ) {
				return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
			}
		}

		if( ( $row = reset( $result ) ) !== false )
		{
			$row['siteid'] = $siteId;
			return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
		}


		// Try to find the best matching locale
		$search = $this->createSearch( $active );

		$expr = array (
			$search->compare( '==', 'locale.siteid', $sitePath ),
			$search->getConditions()
		);

		if ( $active === true )
		{
			$expr[] = $search->compare( '>', 'locale.currency.status', 0 );
			$expr[] = $search->compare( '>', 'locale.language.status', 0 );
			$expr[] = $search->compare( '>', 'locale.site.status', 0 );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'locale.position' ) ) );
		$result = $this->_search( $search );

		// Try to find first item where site and language matches
		foreach( $result as $row )
		{
			if( $row['siteid'] == $siteId && $row['langid'] == $lang ) {
				return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
			}
		}

		// Try to find first item where language matches
		foreach( $result as $row )
		{
			if( $row['langid'] == $lang )
			{
				$row['siteid'] = $siteId;
				return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
			}
		}

		// Try to find first item where site matches
		foreach( $result as $row )
		{
			if( $row['siteid'] == $siteId ) {
				return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
			}
		}

		// Return first item (no other match found)
		if( ( $row = reset( $result ) ) !== false )
		{
			$row['siteid'] = $siteId;
			return $this->_createItem( $row, $siteItem, $sitePath, $siteSubTree );
		}

		throw new MShop_Locale_Exception( sprintf( 'Locale item for site "%1$s" not found', $site ) );
	}


	/**
	 * Instances a new locale item object.
	 *
	 * @param array $values Parameter to initialise the item
	 * @param MShop_Locale_Item_Site_Interface $site Site item
	 * @param array $sitePath List of site IDs up to the root site
	 * @param array $siteSubTree List of site IDs below and including the current site
	 * @return MShop_Locale_Item_Default Locale item
	 */
	protected function _createItem( array $values = array( ), MShop_Locale_Item_Site_Interface $site = null,
		array $sitePath = array(), array $siteSubTree = array() )
	{
		return new MShop_Locale_Item_Default( $values, $site, $sitePath, $siteSubTree );
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
		$stmt = $conn->create($sql);

		$this->_getContext()->getLogger()->log(__METHOD__ . ': SQL statement: ' . $stmt, MW_Logger_Abstract::DEBUG);

		return $stmt->execute();
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Criteria object with conditions, sortations, etc.
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	protected function _search( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

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

			$path = 'mshop/locale/manager/default/item/search';
			$sql = $config->get($path, $path);
			$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

			try
			{
				while ( ( $row = $results->fetch() ) !== false ) {
					$items[ $row['id'] ] = $row;
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			if ( $total !== null )
			{
				$path = 'mshop/locale/manager/default/item/count';
				$sql = $config->get($path, $path);
				$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Locale_Exception( sprintf( 'Total results value not found' ) );
				}

				$total = $row['count'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}
}
