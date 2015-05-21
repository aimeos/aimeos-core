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
	extends Controller_Common_Product_Import_Csv_Processor_Abstract
	implements Controller_Common_Product_Import_Csv_Processor_Interface
{
	private $_cache;
	private $_listTypes;


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

		/** controller/jobs/product/import/csv/processor/catalog/listtypes
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
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/processor/attribute/listtypes
		 * @see controller/jobs/product/import/csv/processor/media/listtypes
		 * @see controller/jobs/product/import/csv/processor/price/listtypes
		 * @see controller/jobs/product/import/csv/processor/product/listtypes
		 * @see controller/jobs/product/import/csv/processor/text/listtypes
		 */
		$this->_listTypes = $context->getConfig()->get( 'controller/jobs/product/import/csv/processor/catalog/listtypes' );

		$this->_cache = $this->_getCache( 'catalog' );
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
		$context = $this->_getContext();
		$listManager = MShop_Factory::createManager( $context, 'catalog/list' );
		$manager = MShop_Factory::createManager( $context, 'catalog' );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = array();
			$map = $this->_getMappedChunk( $data );
			$listItems = $this->_getListItems( $product->getId(), $this->_listTypes );

			foreach( $listItems as $listId => $listItem )
			{
				if( isset( $map[$pos] ) && ( !isset( $map[$pos]['catalog.code'] )
					|| $this->_cache->get( $map[$pos]['catalog.code'] ) == $listItem->getParentId() )
				) {
					$pos++;
					continue;
				}

				$listItems[$listId] = null;
				$delete[] = $listId;
				$pos++;
			}

			$listManager->deleteItems( $delete );

			foreach( $map as $pos => $list )
			{
				if( !isset( $map[$pos]['catalog.code'] ) || $list['catalog.code'] === '' || isset( $list['catalog.list.type'] )
					&& $this->_listTypes !== null && !in_array( $list['catalog.list.type'], (array) $this->_listTypes )
				) {
					continue;
				}

				if( ( $catid = $this->_cache->get( $list['catalog.code'] ) ) === null )
				{
					$msg = 'No category for code "%1$s" available when importing product with code "%2$s"';
					throw new Controller_Jobs_Exception( sprintf( $msg, $list['catalog.code'], $product->getCode() ) );
				}

				if( ( $listItem = array_shift( $listItems ) ) === null ) {
					$listItem = $listManager->createItem();
				}

				$typecode = ( isset( $list['catalog.list.type'] ) ? $list['catalog.list.type'] : 'default' );
				$list['catalog.list.typeid'] = $this->_getTypeId( 'catalog/list/type', 'product', $typecode );
				$list['catalog.list.parentid'] = $catid;
				$list['catalog.list.refid'] = $product->getId();
				$list['catalog.list.domain'] = 'product';

				$listItem->fromArray( $this->_addListItemDefaults( $list, $pos ) );
				$listManager->saveItem( $listItem );
			}

			$remaining = $this->_getObject()->process( $product, $data );

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
	protected function _addListItemDefaults( array $list, $pos )
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
	protected function _getListItems( $prodid, $types )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'catalog/list' );
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
}
