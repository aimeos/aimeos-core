<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Default.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */

/**
 * Index sub-manager for product attributes.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Attribute_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Attribute_Interface
{
	private $_searchConfig = array(
		'catalog.index.attribute.id' => array(
			'code'=>'catalog.index.attribute.id',
			'internalcode'=>':site AND mcatinat."attrid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_attribute" AS mcatinat ON mcatinat."prodid" = mpro."id"' ),
			'label'=>'Product index attribute ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'catalog.index.attribute.code' => array(
			'code'=>'catalog.index.attribute.code()',
			'internalcode'=>':site AND mcatinat."listtype" = $1 AND mcatinat."type" = $2 AND mcatinat."code"',
			'label'=>'Attribute code, parameter(<list type code>,<attribute type code>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.attributecount' => array(
			'code'=>'catalog.index.attributecount()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinat2."attrid")
				FROM "mshop_catalog_index_attribute" AS mcatinat2
				WHERE mpro."id" = mcatinat2."prodid" AND :site
				AND mcatinat2."listtype" = $1 AND mcatinat2."attrid" IN ( $2 ) )',
			'label'=>'Number of product attributes, parameter(<list type code>,<attribute IDs>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$site = $context->getLocale()->getSitePath();
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'siteid', null ),
			$search->compare( '==', 'siteid', $site ),
		);
		$search->setConditions( $search->combine( '||', $expr ) );

		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinat."siteid"' ) );
		$this->_searchConfig['catalog.index.attribute.id']['internalcode'] =
			str_replace( ':site', $string, $this->_searchConfig['catalog.index.attribute.id']['internalcode'] );

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.attribute.code'], 'mcatinat."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.attributecount'], 'mcatinat2."siteid"', $site );
	}


	/**
	 * Creates new attribute item object.
	 *
	 * @return MShop_Attribute_Item_Interface New product item
	 */
	public function createItem()
	{
		return MShop_Product_Manager_Factory::createManager( $this->_getContext() )->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return MShop_Product_Manager_Factory::createManager( $this->_getContext() )->createSearch( $default );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$path = 'mshop/catalog/manager/index/attribute/default/optimize';

		if( ( $sql = $config->get( $path, null ) ) === null ) {
			return;
		}

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $conn->create( $sql )->execute()->finish();
			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Removes an item from the index.
	 *
	 * @param integer $id Product ID
	 */
	public function deleteItem( $id )
	{
		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/attribute/default/item/delete' );
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
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
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		foreach( $this->_searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );
		$list = array_merge( $list, $productManager->getSearchAttributes() );

		if( $withsub === true )
		{
			$path = 'mshop/catalog/manager/index/attribute/default/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
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
	 * Returns the attribute item for the given ID
	 *
	 * @param integer $id Id of item
	 * @return MShop_Attribute_Item_Interface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return MShop_Product_Manager_Factory::createManager( $this->_getContext() )->getItem( $id, $ref );
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
		return $this->_getSubManager( 'catalog', 'index/attribute/' . $manager, $name );
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
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
			$cfgPathSearch = 'mshop/catalog/manager/index/attribute/default/item/search';
			$cfgPathCount =  'mshop/catalog/manager/index/attribute/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total );

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

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $ids ) );
		$products = $productManager->searchItems( $search, $ref, $total );

		foreach( $ids as $id )
		{
			if( isset( $products[$id] ) ) {
				$items[ $id ] = $products[ $id ];
			}
		}

		return $items;
	}


	/**
	 * Rebuilds the catalog index attribute for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items List of product items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$path = 'mshop/catalog/manager/index/price/default/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->rebuildIndex( $items );
		}

		$this->_begin();


		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{

			foreach ( $items as $item )
			{
				$listTypes = array();
				foreach( $item->getListItems( 'attribute' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}

				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/attribute/default/item/insert' );

				foreach( $item->getRefItems( 'attribute' ) as $refItem )
				{
					if( !isset( $listTypes[ $refItem->getId() ] ) )
					{
						$msg = sprintf( 'No list type for attribute item with ID "%1$s"', $refItem->getId() );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[ $refItem->getId() ] as $listType )
					{
						$stmt->bind( 1, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 3, $refItem->getId(), MW_DB_Statement_Abstract::PARAM_INT );
						$stmt->bind( 4, $listType );
						$stmt->bind( 5, $refItem->getType() );
						$stmt->bind( 6, $refItem->getCode() );
						$stmt->bind( 7, $date ); // mtime
						$stmt->bind( 8, $editor );
						$stmt->bind( 9, $date ); // ctime
						$result = $stmt->execute()->finish();
					}
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		$this->_commit();
	}
}