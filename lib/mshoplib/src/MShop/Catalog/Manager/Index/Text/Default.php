<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Submanager for text.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Text_Default
	extends MShop_Catalog_Manager_Index_DBBase
	implements MShop_Catalog_Manager_Index_Text_Interface
{
	private $_searchConfig = array(
		'catalog.index.text.id' => array(
			'code'=>'catalog.index.text.id',
			'internalcode'=>'mcatinte."textid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_text" AS mcatinte ON mcatinte."prodid" = mpro."id"' ),
			'label'=>'Product index text ID',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.text.relevance' => array(
			'code'=>'catalog.index.text.relevance()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinte2."prodid")
				FROM "mshop_catalog_index_text" AS mcatinte2
				WHERE mpro."id" = mcatinte2."prodid" AND :site AND mcatinte2."listtype" = $1
				AND ( mcatinte2."langid" = $2 OR mcatinte2."langid" IS NULL ) AND POSITION( $3 IN mcatinte2."value" ) > 0 )',
			'label'=>'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type'=> 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'sort:catalog.index.text.relevance' => array(
			'code'=>'sort:catalog.index.text.relevance()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinte2."prodid")
				FROM "mshop_catalog_index_text" AS mcatinte2
				WHERE mpro."id" = mcatinte2."prodid" AND :site AND mcatinte2."listtype" = $1
				AND ( mcatinte2."langid" = $2 OR mcatinte2."langid" IS NULL ) AND POSITION( $3 IN mcatinte2."value" ) > 0 )',
			'label'=>'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type'=> 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'catalog.index.text.value' => array(
			'code'=>'catalog.index.text.value()',
			'internalcode'=>':site AND mcatinte."listtype" = $1 AND ( mcatinte."langid" = $2 OR mcatinte."langid" IS NULL ) AND mcatinte."type" = $3 AND mcatinte."domain" = $4 AND mcatinte."value"',
			'label'=>'Product text by type, parameter(<list type code>,<language ID>,<text type code>,<domain>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.text.value' => array(
			'code'=>'sort:catalog.index.text.value()',
			'internalcode'=>'mcatinte."value"',
			'label'=>'Sort product text by type, parameter(<list type code>,<language ID>,<text type code>,<domain>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		)
	);

	private $_langIds;
	private $_subManagers;


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.value'], 'mcatinte."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.relevance'], 'mcatinte2."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['sort:catalog.index.text.relevance'], 'mcatinte2."siteid"', $site );
	}


	/**
	 * Counts the number products that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key (usually the ID) to aggregate products for
	 * @return array List of ID values as key and the number of counted products as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		return $this->_aggregate( $search, $key, 'mshop/catalog/manager/index/default/aggregate' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		parent::cleanup( $siteids );

		$this->_cleanup( $siteids, 'mshop/catalog/manager/index/text/default/item/delete' );
	}


	/**
	 * Removes all entries not touched after the given timestamp in the catalog index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		$this->_doCleanupIndex( $timestamp, 'mshop/catalog/manager/index/text/default/cleanup' );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->_doDeleteItems( $ids, 'mshop/catalog/manager/index/text/default/item/delete' );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = parent::getSearchAttributes( $withsub );

		/** classes/catalog/manager/index/text/submanagers
		 * List of manager names that can be instantiated by the catalog index text manager
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
		$path = 'classes/catalog/manager/index/text/submanagers';

		$list += $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/catalog/manager/index/text/name
		 * Class name of the used catalog index text manager implementation
		 *
		 * Each default catalog index text manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_Index_Text_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_Index_Text_Mytext
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/index/text/name = Mytext
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyText"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/catalog/manager/index/text/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog index text manager.
		 *
		 *  mshop/catalog/manager/index/text/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog index text manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/text/decorators/global
		 * @see mshop/catalog/manager/index/text/decorators/local
		 */

		/** mshop/catalog/manager/index/text/decorators/global
		 * Adds a list of globally available decorators only to the catalog index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index text manager.
		 *
		 *  mshop/catalog/manager/index/text/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/text/decorators/excludes
		 * @see mshop/catalog/manager/index/text/decorators/local
		 */

		/** mshop/catalog/manager/index/text/decorators/local
		 * Adds a list of local decorators only to the catalog index text manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index text manager.
		 *
		 *  mshop/catalog/manager/index/text/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/text/decorators/excludes
		 * @see mshop/catalog/manager/index/text/decorators/global
		 */

		return $this->_getSubManager( 'catalog', 'index/text/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$this->_doOptimize( 'mshop/catalog/manager/index/text/default/optimize' );
	}


	/**
	 * Rebuilds the catalog index text for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param MShop_Common_Item_Interface[] $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$context = $this->_getContext();
		$sites = $context->getLocale()->getSitePath();
		$siteid = $context->getLocale()->getSiteId();
		$langIds = $this->_getLanguageIds( $sites );
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );


		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			foreach( $items as $item )
			{
				$parentId = $item->getId(); //  id is not $item->getId() for sub-products

				$listTypes = array();
				foreach( $item->getListItems( 'text' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}

				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/text/default/item/insert' );

				foreach( $item->getRefItems( 'text' ) as $refId => $refItem )
				{
					if( !isset( $listTypes[$refId] ) ) {
						$msg = sprintf( 'List type for text item with ID "%1$s" not available', $refId );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[$refId] as $listType )
					{
						$this->_saveText(
							$stmt, $parentId, $siteid, $refId, $refItem->getLanguageId(), $listType,
							$refItem->getType(), 'product', $refItem->getContent(), $date, $editor
						);
					}
				}

				$nameList = array();
				foreach( $item->getRefItems( 'text', 'name' ) as $refItem ) {
					$nameList[ $refItem->getLanguageId() ] = $refItem;
				}

				foreach( $langIds as $langId )
				{
					if( !isset( $nameList[$langId] ) )
					{
						$this->_saveText(
							$stmt, $parentId, $siteid, null, $langId, 'default',
							'name', 'product', $item->getLabel(), $date, $editor
						);
					}
				}
			}

			$dbm->release( $conn, $dbname );

		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$this->_saveAttributeTexts( $items );

		foreach( $this->_getSubManagers() as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @return array List of items implementing MShop_Product_Item_Interface with ids as keys
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$cfgPathSearch = 'mshop/catalog/manager/index/text/default/item/search';
		$cfgPathCount = 'mshop/catalog/manager/index/text/default/item/count';

		return $this->_doSearchItems( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Returns product IDs and texts that matches the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function searchTexts( MW_Common_Criteria_Interface $search )
	{
		$list = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/text/default/text/search';
			$required = array( 'product' );

			$total = null;
			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, '', $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$list[ $row['prodid'] ] = $row['value'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $list;
	}


	/**
	 * Saves texts associated with attributes to catalog_index_text.
	 *
	 * @param MShop_Common_Item_Interface[] $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	protected function _saveAttributeTexts( array $items )
	{
		$prodIds = array();

		foreach( $items as $item )
		{
			foreach( $item->getRefItems( 'attribute', null, 'default' ) as $attrItem ) {
				$prodIds[$attrItem->getId()][] = $item->getId();
			}
		}

		if( empty( $prodIds ) ) { return; }


		$attrManager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );
		$search = $attrManager->createSearch(true);
		$expr = array(
			$search->compare( '==', 'attribute.id', array_keys( $prodIds ) ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		$attributeItems = $attrManager->searchItems( $search, array( 'text' ) );


		$context = $this->_getContext();
		$locale = $context->getLocale();
		$siteid = $context->getLocale()->getSiteId();
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );


		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/text/default/item/insert' );

			foreach( $attributeItems as $id => $item )
			{
				$listTypes = array();
				foreach( $item->getListItems( 'text', 'default' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}

				foreach( $item->getRefItems( 'text' ) as $refId => $refItem )
				{
					if( !isset( $listTypes[$refId] ) ) {
						$msg = sprintf( 'List type for text item with ID "%1$s" not available', $refId );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[$refId] as $listType )
					{
						foreach( $prodIds[$id] as $productId )
						{
							$this->_saveText(
								$stmt, $productId, $siteid, $refId, $refItem->getLanguageId(), $listType,
								$refItem->getType(), 'attribute', $refItem->getContent(), $date, $editor
							);
						}
					}
				}

				$names = $item->getRefItems( 'text', 'name' );

				if( empty( $names ) )
				{
					$this->_saveText(
						$stmt, $prodIds[$id], $siteid, null, $locale->getLanguageId(), 'default',
						'name', 'attribute', $item->getLabel(), $date, $editor
					);
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Saves the text record with given set of parameters.
	 *
	 * @param MW_DB_Statement_Interface $stmt Prepared SQL statement with place holders
	 * @param integer $id ID of the product item
	 * @param integer $siteid Site ID
	 * @param string $refid ID of the text item that contains the text
	 * @param string $lang Two letter ISO language code
	 * @param string $listtype Type of the referenced text in the list item
	 * @param string $reftype Type of the referenced text item
	 * @param string $domain Domain the text is from
	 * @param string $content Text content to store
	 * @param string $date Current timestamp in "YYYY-MM-DD HH:mm:ss" format
	 * @param string $editor Name of the editor who stored the product
	 */
	protected function _saveText( MW_DB_Statement_Interface $stmt, $id, $siteid, $refid, $lang, $listtype,
		$reftype, $domain, $content, $date, $editor )
	{
		$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
		$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
		$stmt->bind( 3, $refid );
		$stmt->bind( 4, $lang );
		$stmt->bind( 5, $listtype );
		$stmt->bind( 6, $reftype );
		$stmt->bind( 7, $domain );
		$stmt->bind( 8, $content );
		$stmt->bind( 9, $date );//mtime
		$stmt->bind( 10, $editor );
		$stmt->bind( 11, $date );//ctime

		try {
			$stmt->execute()->finish();
		} catch( MW_DB_Exception $e ) { ; } // Ignore duplicates
	}


	/**
	 * Returns the list of sub-managers available for the catalog index attribute manager.
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	protected function _getSubManagers()
	{
		if( $this->_subManagers === null )
		{
			$this->_subManagers = array();
			$path = 'classes/catalog/manager/index/text/submanagers';

			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$this->_subManagers[$domain] = $this->getSubManager( $domain );
			}

			return $this->_subManagers;
		}

		return $this->_subManagers;
	}


	/**
	 * Returns the configured langauge IDs for the given sites
	 *
	 * @param array $siteIds List of site IDs
	 * @return array List of language IDs
	 */
	protected function _getLanguageIds( array $siteIds )
	{
		if( !isset( $this->_langIds ) )
		{
			$list = array();
			$manager = MShop_Factory::createManager( $this->_getContext(), 'locale' );

			$search = $manager->createSearch( true );
			$search->setConditions( $search->compare( '==', 'locale.siteid', $siteIds ) );

			foreach( $manager->searchItems( $search ) as $item ) {
				$list[ $item->getLanguageId() ] = null;
			}

			$this->_langIds = array_keys( $list );
		}

		return $this->_langIds;
	}
}