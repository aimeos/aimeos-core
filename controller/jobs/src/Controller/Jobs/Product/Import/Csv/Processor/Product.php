<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Product processor for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Processor_Product
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
	public function save( MShop_Product_Item_Interface $product, array $data )
	{
		$context = $this->_getContext();
		$listManager = MShop_Factory::createManager( $context, 'product/list' );
		$manager = MShop_Factory::createManager( $context, 'product' );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = $prodcodes = array();
			$listItems = $product->getListItems( 'product' );
			$map = $this->_getMappedProductData( $data, $prodcodes );

			foreach( $listItems as $listId => $listItem )
			{
				$refItem = $listItem->getRefItem();

				if( isset( $map[$pos] ) && ( !isset( $map[$pos]['product.code'] )
					|| ( $refItem !== null && $map[$pos]['product.code'] === $refItem->getCode() ) )
				) {
					unset( $map[$pos] );
					continue;
				}

				$delete[] = $listId;
				$pos++;
			}

			$listManager->deleteItems( $delete );
			$products = $this->_getProducts( $prodcodes, array() );

			foreach( $map as $pos => $list )
			{
				if( $list['product.code'] === '' ) {
					continue;
				}

				if( !isset( $products[ $list['product.code'] ] ) )
				{
					$msg = 'No product for code "%1$s" available when importing product with code "%2$s"';
					$context->getLogger()->log( sprintf( $msg, $list['product.code'], $product->getCode() ) );
					continue;
				}

				$listItem = $listManager->createItem();

				$typecode = ( isset( $list['product.list.type'] ) ? $list['product.list.type'] : 'default' );
				$list['product.list.typeid'] = $this->_getTypeId( 'product/list/type', 'product', $typecode );
				$list['product.list.refid'] = $products[ $list['product.code'] ]->getId();
				$list['product.list.parentid'] = $product->getId();
				$list['product.list.domain'] = 'product';
				$list['product.list.position'] = $pos;

				$listItem->fromArray( $this->_addListItemDefaults( $list ) );
				$listManager->saveItem( $listItem );
			}

			$remaining = $this->_getObject()->save( $product, $data );

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
	 * Returns the chunked data with text and product list properties in each chunk
	 *
	 * @param array $data List of CSV fields with position as key and domain item key as value
	 * @param array &$prodcodes List that will contain the found product codes afterwards
	 * @return array List of associative arrays containing the chunked properties
	 */
	protected function _getMappedProductData( array &$data, array &$prodcodes )
	{
		$idx = 0;
		$map = array();

		foreach( $this->_getMapping() as $pos => $key )
		{
			if( isset( $map[$idx][$key] ) ) {
				$idx++;
			}

			if( isset( $data[$pos] ) )
			{
				if( $key === 'product.code' && $data[$pos] !== '' ) {
					$prodcodes[] = $data[$pos];
				}

				$map[$idx][$key] = $data[$pos];
				unset( $data[$pos] );
			}
		}

		return $map;
	}
}
