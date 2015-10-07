<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Catalog;


/**
 * Catalog processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Processor\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface
{
	private $cache;
	private $listTypes;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object Decorated processor
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $mapping,
		\Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object = null )
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
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( \Aimeos\MShop\Product\Item\Iface $product, array $data )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'catalog' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'catalog/lists' );

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
				if( !isset( $list['catalog.code'] ) || $list['catalog.code'] === '' || isset( $list['catalog.lists.type'] )
					&& $this->listTypes !== null && !in_array( $list['catalog.lists.type'], (array) $this->listTypes )
				) {
					continue;
				}

				$codes = explode( $separator, $list['catalog.code'] );
				$type = ( isset( $list['catalog.lists.type'] ) ? $list['catalog.lists.type'] : 'default' );

				foreach( $codes as $code )
				{
					if( ( $catid = $this->cache->get( $code ) ) === null )
					{
						$msg = 'No category for code "%1$s" available when importing product with code "%2$s"';
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( $msg, $code, $product->getCode() ) );
					}

					if( ( $listItem = array_shift( $listItems ) ) === null ) {
						$listItem = $listManager->createItem();
					}

					$list['catalog.lists.typeid'] = $this->getTypeId( 'catalog/lists/type', 'product', $type );
					$list['catalog.lists.parentid'] = $catid;
					$list['catalog.lists.refid'] = $prodid;
					$list['catalog.lists.domain'] = 'product';

					$listItem->fromArray( $this->addListItemDefaults( $list, $pos++ ) );
					$listManager->saveItem( $listItem );
				}
			}

			$remaining = $this->getObject()->process( $product, $data );

			$manager->commit();
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return $remaining;
	}


	/**
	 * Adds the list item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "catalog.lists.status" => 1
	 * @param integer $pos Computed position of the list item in the associated list of items
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addListItemDefaults( array $list, $pos )
	{
		if( !isset( $list['catalog.lists.position'] ) ) {
			$list['catalog.lists.position'] = $pos;
		}

		if( !isset( $list['catalog.lists.status'] ) ) {
			$list['catalog.lists.status'] = 1;
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
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );
		$search = $manager->createSearch();

		$expr = array(
			$search->compare( '==', 'catalog.lists.domain', 'product' ),
			$search->compare( '==', 'catalog.lists.refid', $prodid ),
		);

		if( $types !== null ) {
			$expr[] = $search->compare( '==', 'catalog.lists.type.code', $types );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'catalog.lists.position' ) ) );
		$search->setSlice( 0, 0x7FFFFFFF );

		return $manager->searchItems( $search );
	}


	/**
	 * Returns the pool of list items that can be reassigned
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item object
	 * @param array $map List of associative arrays containing the chunked properties
	 * @return array List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItemPool( \Aimeos\MShop\Product\Item\Iface $product, array $map )
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

		$listManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog/lists' );
		$listManager->deleteItems( $delete );

		return $listItems;
	}
}
