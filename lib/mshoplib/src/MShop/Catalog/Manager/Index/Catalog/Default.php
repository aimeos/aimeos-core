<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Catalog
 */


/**
 * Submanager for catalog.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Catalog_Default
	extends MShop_Catalog_Manager_Index_DBBase
	implements MShop_Catalog_Manager_Index_Catalog_Interface
{
	private $_searchConfig = array(
		'catalog.index.catalog.id' => array(
			'code'=>'catalog.index.catalog.id',
			'internalcode'=>'mcatinca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_catalog" AS mcatinca ON mcatinca."prodid" = mpro."id"' ),
			'label'=>'Product index category ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.catalogaggregate' => array(
			'code'=>'catalog.index.catalogaggregate()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinca_agg."catid")
				FROM "mshop_catalog_index_catalog" AS mcatinca_agg
				WHERE mpro."id" = mcatinca_agg."prodid" AND :site
				AND mcatinca_agg."catid" IN ( $1 ) )',
			'label'=>'Number of product categories, parameter(<category IDs>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.catalogcount' => array(
			'code'=>'catalog.index.catalogcount()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinca_cnt."catid")
				FROM "mshop_catalog_index_catalog" AS mcatinca_cnt
				WHERE mpro."id" = mcatinca_cnt."prodid" AND :site
				AND mcatinca_cnt."catid" IN ( $2 ) AND mcatinca_cnt."listtype" = $1 )',
			'label'=>'Number of product categories, parameter(<list type code>,<category IDs>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.catalog.position' => array(
			'code'=>'catalog.index.catalog.position()',
			'internalcode'=>':site AND mcatinca."catid" = $2 AND mcatinca."listtype" = $1 AND mcatinca."pos"',
			'label'=>'Product position in category, parameter(<list type code>,<category ID>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'sort:catalog.index.catalog.position' => array(
			'code'=>'sort:catalog.index.catalog.position()',
			'internalcode'=>'mcatinca."pos"',
			'label'=>'Sort product position in category, parameter(<list type code>,<category ID>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		)
	);

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

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.catalog.position'], 'mcatinca."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.catalogaggregate'], 'mcatinca_agg."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.catalogcount'], 'mcatinca_cnt."siteid"', $site );
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

		$this->_cleanup( $siteids, 'mshop/catalog/manager/index/catalog/default/item/delete' );
	}


	/**
	 * Removes all entries not touched after the given timestamp in the catalog index.
	 * This can be a long lasting operation.
	 *
	 * @param string $timestamp Timestamp in ISO format (YYYY-MM-DD HH:mm:ss)
	 */
	public function cleanupIndex( $timestamp )
	{
		$this->_doCleanupIndex( $timestamp, 'mshop/catalog/manager/index/catalog/default/cleanup' );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		$this->_doDeleteItems( $ids, 'mshop/catalog/manager/index/catalog/default/item/delete' );
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

		/** classes/catalog/manager/index/attribute/submanagers
		 * List of manager names that can be instantiated by the catalog index attribute manager
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
		$path = 'classes/catalog/manager/index/attribute/submanagers';

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
		/** classes/catalog/manager/index/catalog/name
		 * Class name of the used catalog index catalog manager implementation
		 *
		 * Each default catalog index catalog manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Catalog_Manager_Index_Catalog_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Catalog_Manager_Index_Catalog_Mycatalog
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/catalog/manager/index/catalog/name = Mycatalog
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

		/** mshop/catalog/manager/index/catalog/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the catalog index catalog manager.
		 *
		 *  mshop/catalog/manager/index/catalog/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the catalog index catalog manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/catalog/decorators/global
		 * @see mshop/catalog/manager/index/catalog/decorators/local
		 */

		/** mshop/catalog/manager/index/catalog/decorators/global
		 * Adds a list of globally available decorators only to the catalog index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index catalog manager.
		 *
		 *  mshop/catalog/manager/index/catalog/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the catalog controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/catalog/decorators/excludes
		 * @see mshop/catalog/manager/index/catalog/decorators/local
		 */

		/** mshop/catalog/manager/index/catalog/decorators/local
		 * Adds a list of local decorators only to the catalog index catalog manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the catalog index catalog manager.
		 *
		 *  mshop/catalog/manager/index/catalog/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the catalog
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/catalog/manager/index/catalog/decorators/excludes
		 * @see mshop/catalog/manager/index/catalog/decorators/global
		 */

		return $this->_getSubManager( 'catalog', 'index/catalog/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$this->_doOptimize( 'mshop/catalog/manager/index/catalog/default/optimize' );
	}


	/**
	 * Rebuilds the catalog index catalog for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param MShop_Common_Item_Interface[] $items Associative list of product IDs and items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$ids = $listItems = array();
		$context = $this->_getContext();
		$listManager = MShop_Factory::createManager( $context, 'catalog/list' );

		foreach( $items as $id => $item ) {
			$ids[] = $id;
		}

		$search = $listManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'catalog.list.refid', $ids ),
			$search->compare( '==', 'catalog.list.domain', 'product' ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7FFFFFFF );

		$result = $listManager->searchItems( $search );

		foreach( $result as $listItem ) {
			$listItems[$listItem->getRefId()][] = $listItem;
		}

		$date = date( 'Y-m-d H:i:s' );
		$editor = $context->getEditor();
		$siteid = $context->getLocale()->getSiteId();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			foreach( $items as $id => $item )
			{
				$parentId = $item->getId(); // $id is not $item->getId() for sub-products
				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/catalog/default/item/insert' );

				if( !array_key_exists( $parentId, $listItems ) ) { continue; }

				foreach( (array) $listItems[$parentId] as $listItem )
				{
					$stmt->bind( 1, $parentId, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 3, $listItem->getParentId(), MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 4, $listItem->getType() );
					$stmt->bind( 5, $listItem->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 6, $date ); //mtime
					$stmt->bind( 7, $editor );
					$stmt->bind( 8, $date ); //ctime

					try {
						$stmt->execute()->finish();
					} catch( MW_DB_Exception $e ) {; } // Ignore duplicates
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


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
		$cfgPathSearch = 'mshop/catalog/manager/index/catalog/default/item/search';
		$cfgPathCount = 'mshop/catalog/manager/index/catalog/default/item/count';

		return $this->_doSearchItems( $search, $ref, $total, $cfgPathSearch, $cfgPathCount );
	}


	/**
	 * Returns the list of sub-managers available for the catalog index catalog manager.
	 *
	 * @return array Associative list of the sub-domain as key and the manager object as value
	 */
	protected function _getSubManagers()
	{
		if( $this->_subManagers === null )
		{
			$this->_subManagers = array();
			$path = 'mshop/catalog/manager/index/catalog/submanagers';

			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$this->_subManagers[$domain] = $this->getSubManager( $domain );
			}

			return $this->_subManagers;
		}

		return $this->_subManagers;
	}
}