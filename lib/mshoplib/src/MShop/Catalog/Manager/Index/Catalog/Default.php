<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Default.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */

/**
 * Submanager for catalog.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Catalog_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Catalog_Interface
{
	private $_productManager;
	private $_submanagers = array();

	private $_searchConfig = array(
		'catalog.index.catalog.id' => array(
			'code'=>'catalog.index.catalog.id',
			'internalcode'=>':site AND mcatinca."catid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_catalog" AS mcatinca ON mcatinca."prodid" = mpro."id"' ),
			'label'=>'Product index category ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.catalogcount' => array(
			'code'=>'catalog.index.catalogcount()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinca2."catid")
				FROM "mshop_catalog_index_catalog" AS mcatinca2
				WHERE mpro."id" = mcatinca2."prodid" AND :site
				AND mcatinca2."catid" IN ( $2 ) AND mcatinca2."listtype" = $1 )',
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


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_productManager = MShop_Product_Manager_Factory::createManager( $context );


		$site = $context->getLocale()->getSitePath();
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'siteid', null ),
			$search->compare( '==', 'siteid', $site ),
		);
		$search->setConditions( $search->combine( '||', $expr ) );

		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinca."siteid"' ) );
		$this->_searchConfig['catalog.index.catalog.id']['internalcode'] =
			str_replace( ':site', $string, $this->_searchConfig['catalog.index.catalog.id']['internalcode'] );

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.catalog.position'], 'mcatinca."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.catalogcount'], 'mcatinca2."siteid"', $site );


		$confpath = 'mshop/catalog/manager/index/catalog/default/submanagers';

		foreach( $context->getConfig()->get( $confpath, array() ) as $domain ) {
			$this->_submanagers[ $domain ] = $this->getSubManager( $domain );
		}
	}


	/**
	 * Creates new catalog item object.
	 *
	 * @return MShop_Catalog_Item_Interface New product item
	 */
	public function createItem()
	{
		return $this->_productManager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->_productManager->createSearch( $default );
	}


	/**
	 * Removes an item from the index.
	 *
	 * @param integer $id Product ID
	 */
	public function deleteItem( $id )
	{
		$this->deleteItems( array( $id ) );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		foreach( $this->_submanagers as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();

		$sql = $context->getConfig()->get( 'mshop/catalog/manager/index/catalog/default/item/delete' );
		
		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'prodid', $ids ) );
		
		$types = array( 'prodid' => MW_DB_Statement_Abstract::PARAM_STR );
		$translations = array( 'prodid' => '"prodid"' );
		
		$cond = $search->getConditionString( $types, $translations );
		$sql = str_replace( ':cond', $cond, $sql );
		
		try
		{
			$dbm = $context->getDatabaseManager();
			$conn = $dbm->acquire();

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the catalog item for the given ID
	 *
	 * @param integer $id Id of item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Catalog_Item_Interface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_productManager->getItem( $id, $ref );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		$list = array_merge( $list, $this->_productManager->getSearchAttributes( false ) );

		if( $withsub === true )
		{
			foreach( $this->_submanagers as $submanager ) {
				$list = array_merge( $list, $submanager->getSearchAttributes( $withsub ) );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'catalog', 'index/catalog/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$path = 'mshop/catalog/manager/index/catalog/default/optimize';
			foreach( $context->getConfig()->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Rebuilds the catalog index catalog for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items List of product items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$listItems = array();
		$context = $this->_getContext();
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$listManager = $catalogManager->getSubManager( 'list' );

		$ids = array();
		foreach( $items as $key => $item ) {
			$ids[] = $item->getId();
		}

		$search = $listManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.list.domain', 'product' ),
			$search->compare( '==', 'catalog.list.refid', $ids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7FFFFFFF );

		$result = $listManager->searchItems( $search );

		$listItems = array();
		foreach( $result as $key => $listItem ) {
			$listItems[ $listItem->getRefId() ][] = $listItem;
		}

		$date = date('Y-m-d H:i:s' );
		$editor = $context->getEditor();
		$siteid = $context->getLocale()->getSiteId();

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			foreach ( $items as $key => $item )
			{
				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/catalog/default/item/insert' );

				if( !array_key_exists( $item->getId(), $listItems ) ) { continue; }
				
				foreach ( $listItems[ $item->getId() ] as $listItem )
				{
					$stmt->bind( 1, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 3, $listItem->getParentId(), MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 4, $listItem->getType() );
					$stmt->bind( 5, $listItem->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
					$stmt->bind( 6, $date );//mtime
					$stmt->bind( 7, $editor );
					$stmt->bind( 8, $date );//ctime
					$stmt->execute()->finish();
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Stores a new item in the index.
	 *
	 * @param MShop_Common_Item_Interface $item Product item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$this->rebuildIndex( array( $item ) );
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
		$items = $ids = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/catalog/default/item/search';
			$cfgPathCount =  'mshop/catalog/manager/index/catalog/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )	{
				$ids[] = $row['id'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		$search = $this->_productManager->createSearch();
		$search->setConditions( $search->compare('==', 'product.id', $ids) );
		$products = $this->_productManager->searchItems( $search, $ref, $total );

		foreach ($ids as $id) {
			if( isset( $products[$id] ) ) {
				$items[ $id ] = $products[ $id ];
			}
		}

		return $items;
	}
}