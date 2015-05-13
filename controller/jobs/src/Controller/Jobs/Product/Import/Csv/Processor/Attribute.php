<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Attribute processor for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Processor_Attribute
	extends Controller_Jobs_Product_Import_Csv_Processor_Abstract
	implements Controller_Jobs_Product_Import_Csv_Processor_Interface
{
	private $_attributes = array();


	/**
	 * Saves the attribute related data to the storage
	 *
	 * @param MShop_Product_Item_Interface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Interface $product, array $data )
	{
		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'attribute' );
		$listManager = MShop_Factory::createManager( $context, 'product/list' );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = array();
			$map = $this->_getMappedData( $data );
			$listItems = $product->getListItems( 'attribute' );

			foreach( $listItems as $listId => $listItem )
			{
				if( isset( $map[$pos] ) )
				{
					if( !isset( $map[$pos]['attribute.code'] ) || !isset( $map[$pos]['attribute.type'] ) )
					{
						unset( $map[$pos] );
						continue;
					}

					$refItem = $listItem->getRefItem();

					if( $refItem !== null && $map[$pos]['attribute.code'] === $refItem->getCode()
						&& $map[$pos]['attribute.type'] === $refItem->getType()
						&& ( !isset( $map[$pos]['product.list.type'] ) || isset( $map[$pos]['product.list.type'] )
						&& $map[$pos]['product.list.type'] === $listItem->getType() )
					) {
						unset( $map[$pos] );
						continue;
					}
				}

				$delete[] = $listId;
				$pos++;
			}

			$listManager->deleteItems( $delete );

			foreach( $map as $pos => $list )
			{
				if( $list['attribute.code'] === '' || $list['attribute.type'] === '' ) {
					continue;
				}

				$attrItem = $this->_getAttributeItem( $list['attribute.code'], $list['attribute.type'] );
				$attrItem->fromArray( $list );
				$manager->saveItem( $attrItem );

				$listItem = $listManager->createItem();

				$typecode = ( isset( $list['product.list.type'] ) ? $list['product.list.type'] : 'default' );
				$list['product.list.typeid'] = $this->_getTypeId( 'product/list/type', 'attribute', $typecode );
				$list['product.list.refid'] = $attrItem->getId();
				$list['product.list.parentid'] = $product->getId();
				$list['product.list.domain'] = 'attribute';
				$list['product.list.position'] = $pos;

				$listItem->fromArray( $this->_addListItemDefaults( $list ) );
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
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string $type Attribute type
	 * @return MShop_Attribute_Item_Interface Attribute item object
	 */
	protected function _getAttributeItem( $code, $type )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $code ),
			$search->compare( '==', 'attribute.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false )
		{
			$item = $manager->createItem();
			$item->setTypeId( $this->_getTypeId( 'attribute/type', 'product', $type ) );
			$item->setCode( $code );
			$item->setLabel( $type . ' ' . $code );
			$item->setStatus( 1 );

			$manager->saveItem( $item );
		}

		return $item;
	}
}
