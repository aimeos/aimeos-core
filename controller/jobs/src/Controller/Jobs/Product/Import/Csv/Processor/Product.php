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
	private $_cache;


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

		$this->_cache = new Controller_Jobs_Product_Import_Csv_Cache_Product( $context );
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
		$listManager = MShop_Factory::createManager( $context, 'product/list' );
		$manager = MShop_Factory::createManager( $context, 'product' );

		$this->_cache->set( $product );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = $prodcodes = array();
			$map = $this->_getMappedData( $data );
			$listItems = $product->getListItems( 'product' );

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

			foreach( $map as $pos => $list )
			{
				if( $list['product.code'] === '' ) {
					continue;
				}

				if( ( $prodid = $this->_cache->get( $list['product.code'] ) ) === null )
				{
					$msg = 'No product for code "%1$s" available when importing product with code "%2$s"';
					$context->getLogger()->log( sprintf( $msg, $list['product.code'], $product->getCode() ) );
					continue;
				}

				$listItem = $listManager->createItem();

				$typecode = ( isset( $list['product.list.type'] ) ? $list['product.list.type'] : 'default' );
				$list['product.list.typeid'] = $this->_getTypeId( 'product/list/type', 'product', $typecode );
				$list['product.list.parentid'] = $product->getId();
				$list['product.list.refid'] = $prodid;
				$list['product.list.domain'] = 'product';
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
}
