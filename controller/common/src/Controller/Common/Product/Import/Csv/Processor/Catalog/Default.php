<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Catalog processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Processor_Catalog_Default
	extends Controller_Common_Product_Import_Csv_Processor_Base
	implements Controller_Common_Product_Import_Csv_Processor_Interface
{
	private $cache;
	private $listTypes;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Common_Product_Import_Csv_Processor_Interface $object Decorated processor
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $mapping,
		Controller_Common_Product_Import_Csv_Processor_Interface $object = null )
	{
		parent::__construct( $context, $mapping, $object );

		/** controller/common/product/import/csv/processor/catalog/listtypes
		 * Names of the catalog list types that are updated or removed
		 *
		 * If you want to associate product items to categories manually via the
		 * administration interface and don't want these to be touched during the
		 * import, you can specify the catalog list types for these products
		 * that shouldn't be updated or removed.
		 *
		 * @param array|null List of catalog list type names or null for all
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/processor/attribute/listtypes
		 * @see controller/common/product/import/csv/processor/media/listtypes
		 * @see controller/common/product/import/csv/processor/price/listtypes
		 * @see controller/common/product/import/csv/processor/product/listtypes
		 * @see controller/common/product/import/csv/processor/text/listtypes
		 */
		$this->listTypes = $context->getConfig()->get( 'controller/common/product/import/csv/processor/catalog/listtypes' );

		$this->cache = $this->getCache( 'catalog' );
	}


	/**
	 * Saves the product related data to the storage
	 *
	 * @param MShop_Product_Item_Interface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Interface $product, array $data )
	{
		$context = $this->getContext();
		$manager = MShop_Factory::createManager( $context, 'catalog' );
		$listManager = MShop_Factory::createManager( $context, 'catalog/list' );

		/** controller/common/product/import/csv/separator
		 * Single separator character for multiple entries in one field of the import file
		 *
		 * The product importer is able split the content of a field from the import
		 * file into several entries based on the given separator character. Thus,
		 * you can create more compact import files and handle a variable range
		 * of entries better. The default separator character is a new line.
		 *
		 * '''Caution:''' The separator character must not be part of any entry
		 * in the field. Otherwise, you will get invalid entries and the importer
		 * may fail!
		 *
		 * @param string Single separator character
		 * @since 2015.07
		 * @category User
		 * @category Developer
		 * @see controller/common/product/import/csv/domains
		 */
		$separator = $context->getConfig()->get( 'controller/common/product/import/csv/separator', "\n" );

		$manager->begin();

		try
		{
			$prodid = $product->getId();
			$map = $this->getMappedChunk( $data );
			$listItems = $this->getListItemPool( $product, $map );

			foreach( $map as $pos => $list )
			{
				if( !isset( $list['catalog.code'] ) || $list['catalog.code'] === '' || isset( $list['catalog.list.type'] )
					&& $this->listTypes !== null && !in_array( $list['catalog.list.type'], (array) $this->listTypes )
				) {
					continue;
				}

				$codes = explode( $separator, $list['catalog.code'] );
				$type = ( isset( $list['catalog.list.type'] ) ? $list['catalog.list.type'] : 'default' );

				foreach( $codes as $code )
				{
					if( ( $catid = $this->cache->get( $code ) ) === null )
					{
						$msg = 'No category for code "%1$s" available when importing product with code "%2$s"';
						throw new Controller_Jobs_Exception( sprintf( $msg, $code, $product->getCode() ) );
					}

					if( ( $listItem = array_shift( $listItems ) ) === null ) {
						$listItem = $listManager->createItem();
					}

					$list['catalog.list.typeid'] = $this->getTypeId( 'catalog/list/type', 'product', $type );
					$list['catalog.list.parentid'] = $catid;
					$list['catalog.list.refid'] = $prodid;
					$list['catalog.list.domain'] = 'product';

					$listItem->fromArray( $this->addListItemDefaults( $list, $pos++ ) );
					$listManager->saveItem( $listItem );
				}
			}

			$remaining = $this->getObject()->process( $product, $data );

			$manager->commit();
		}
		catch( Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return $remaining;
	}


	/**
	 * Adds the list item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "catalog.list.status" => 1
	 * @param integer $pos Computed position of the list item in the associated list of items
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addListItemDefaults( array $list, $pos )
	{
		if( !isset( $list['catalog.list.position'] ) ) {
			$list['catalog.list.position'] = $pos;
		}

		if( !isset( $list['catalog.list.status'] ) ) {
			$list['catalog.list.status'] = 1;
		}

		return $list;
	}


	/**
	 * Returns the catalog list items for the given category and product ID
	 *
	 * @param string $prodid Unique product ID
	 * @param array|null $types List of catalog list types
	 * @return array List of catalog list items
	 */
	protected function getListItems( $prodid, $types )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'catalog/list' );
		$search = $manager->createSearch();

		$expr = array(
			$search->compare( '==', 'catalog.list.domain', 'product' ),
			$search->compare( '==', 'catalog.list.refid', $prodid ),
		);

		if( $types !== null ) {
			$expr[] = $search->compare( '==', 'catalog.list.type.code', $types );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'catalog.list.position' ) ) );
		$search->setSlice( 0, 0x7FFFFFFF );

		return $manager->searchItems( $search );
	}


	/**
	 * Returns the pool of list items that can be reassigned
	 *
	 * @param MShop_Product_Item_Interface $product Product item object
	 * @param array $map List of associative arrays containing the chunked properties
	 * @return array List of list items implementing MShop_Common_Item_List_Interface
	 */
	protected function getListItemPool( MShop_Product_Item_Interface $product, array $map )
	{
		$pos = 0;
		$delete = array();
		$listItems = $this->getListItems( $product->getId(), $this->listTypes );

		foreach( $listItems as $listId => $listItem )
		{
			if( isset( $map[$pos] ) && ( !isset( $map[$pos]['catalog.code'] )
				|| $this->cache->get( $map[$pos]['catalog.code'] ) == $listItem->getParentId() )
			) {
				$pos++;
				continue;
			}

			$listItems[$listId] = null;
			$delete[] = $listId;
			$pos++;
		}

		$listManager = MShop_Factory::createManager( $this->getContext(), 'catalog/list' );
		$listManager->deleteItems( $delete );

		return $listItems;
	}
}
