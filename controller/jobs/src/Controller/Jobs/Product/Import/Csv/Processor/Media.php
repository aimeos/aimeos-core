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
class Controller_Jobs_Product_Import_Csv_Processor_Media
	extends Controller_Jobs_Product_Import_Csv_Processor_Abstract
	implements Controller_Jobs_Product_Import_Csv_Processor_Interface
{
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
				if( !isset( $list['media.url'] ) || $list['media.url'] == '' ) {
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
				$list['product.list.position'] = $pos;

				$listItem->fromArray( $this->_addListItemDefaults( $list ) );
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
