<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Product property processor for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Processor_Property
	extends Controller_Jobs_Product_Import_Csv_Processor_Abstract
	implements Controller_Jobs_Product_Import_Csv_Processor_Interface
{
	/**
	 * Saves the product property related data to the storage
	 *
	 * @param MShop_Product_Item_Interface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Interface $product, array $data )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/property' );
		$manager->begin();

		try
		{
			$pos = 0;
			$delete = array();
			$map = $this->_getMappedData( $data );
			$items = $this->_getPropertyItems( $product->getId() );

			foreach( $items as $id => $item )
			{
				if( isset( $map[$pos] ) )
				{
					if( !isset( $map[$pos]['product.property.type'] ) || !isset( $map[$pos]['product.property.value'] ) )
					{
						unset( $map[$pos] );
						continue;
					}

					if( $map[$pos]['product.property.type'] === $item->getType()
						&& $map[$pos]['product.property.value'] === $item->getValue()
						&& ( !isset( $map[$pos]['product.property.languageid'] )
							|| isset( $map[$pos]['product.property.languageid'] )
							&& $map[$pos]['product.property.languageid'] === $item->getLanguageId()
						)
					) {
						unset( $map[$pos] );
						continue;
					}
				}

				$delete[] = $id;
				$pos++;
			}

			$manager->deleteItems( $delete );

			foreach( $map as $pos => $list )
			{
				if( !isset( $list['product.property.type'] ) || $list['product.property.type'] == ''
					|| !isset( $list['product.property.value'] ) || $list['product.property.value'] == ''
				) {
					continue;
				}

				$typecode = $list['product.property.type'];
				$list['product.property.typeid'] = $this->_getTypeId( 'product/property/type', 'product/property', $typecode );
				$list['product.property.parentid'] = $product->getId();

				$item = $manager->createItem();
				$item->fromArray( $list );
				$manager->saveItem( $item );
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
	 * Returns the product properties for the given product ID
	 *
	 * @param string $prodid Unique product ID
	 * @return array Associative list of product property items
	 */
	protected function _getPropertyItems( $prodid )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/property' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $prodid ) );

		return $manager->searchItems( $search );
	}
}
