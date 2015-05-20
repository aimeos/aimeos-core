<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Media processor for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Processor_Media_Default
	extends Controller_Jobs_Product_Import_Csv_Processor_Abstract
	implements Controller_Jobs_Product_Import_Csv_Processor_Interface
{
	private $_listTypes;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Jobs_Product_Import_Csv_Processor_Interface $object Decorated processor
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $mapping,
			Controller_Jobs_Product_Import_Csv_Processor_Interface $object = null )
	{
		parent::__construct( $context, $mapping, $object );

		/** controller/jobs/product/import/csv/processor/media/listtypes
		 * Names of the product list types for media that are updated or removed
		 *
		 * If you want to associate media items manually via the administration
		 * interface to products and don't want these to be touched during the
		 * import, you can specify the product list types for these media
		 * that shouldn't be updated or removed.
		 *
		 * @param array|null List of product list type names or null for all
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/processor/attribute/listtypes
		 * @see controller/jobs/product/import/csv/processor/product/listtypes
		 * @see controller/jobs/product/import/csv/processor/price/listtypes
		 * @see controller/jobs/product/import/csv/processor/text/listtypes
		 */
		$this->_listTypes = $context->getConfig()->get( 'controller/jobs/product/import/csv/processor/media/listtypes' );
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
		$listManager = MShop_Factory::createManager( $this->_getContext(), 'product/list' );
		$manager = MShop_Factory::createManager( $this->_getContext(), 'media' );
		$manager->begin();

		try
		{
			$listItems = $product->getListItems( 'media' );
			$map = $this->_getMappedData( $data );

			foreach( $map as $pos => $list )
			{
				if( !isset( $list['media.url'] ) || $list['media.url'] === '' || isset( $list['product.list.type'] )
					&& $this->_listTypes !== null && !in_array( $list['product.list.type'], (array) $this->_listTypes )
				) {
					continue;
				}

				if( ( $listItem = array_shift( $listItems ) ) !== null ) {
					$refItem = $listItem->getRefItem();
				} else {
					$listItem = $listManager->createItem();
					$refItem = $manager->createItem();
				}

				$typecode = ( isset( $list['media.type'] ) ? $list['media.type'] : 'default' );
				$list['media.typeid'] = $this->_getTypeId( 'media/type', 'product', $typecode );
				$list['media.domain'] = 'product';

				$refItem->fromArray( $this->_addItemDefaults( $list ) );
				$manager->saveItem( $refItem );

				$typecode = ( isset( $list['product.list.type'] ) ? $list['product.list.type'] : 'default' );
				$list['product.list.typeid'] = $this->_getTypeId( 'product/list/type', 'media', $typecode );
				$list['product.list.parentid'] = $product->getId();
				$list['product.list.refid'] = $refItem->getId();
				$list['product.list.domain'] = 'media';

				$listItem->fromArray( $this->_addListItemDefaults( $list, $pos ) );
				$listManager->saveItem( $listItem );
			}

			foreach( $listItems as $listItem )
			{
				$manager->deleteItem( $listItem->getRefItem()->getId() );
				$listManager->deleteItem( $listItem->getId() );
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
	 * Adds the text item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "media.status" => 1
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function _addItemDefaults( array $list )
	{
		if( !isset( $list['media.label'] ) ) {
			$list['media.label'] = $list['media.url'];
		}

		if( !isset( $list['media.preview'] ) ) {
			$list['media.preview'] = $list['media.url'];
		}

		if( !isset( $list['media.status'] ) ) {
			$list['media.status'] = 1;
		}

		return $list;
	}
}
