<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


/**
 * Product stock processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Product_Import_Csv_Processor_Stock_Default
	extends Controller_Common_Product_Import_Csv_Processor_Base
	implements Controller_Common_Product_Import_Csv_Processor_Interface
{
	private $cache;


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

		$this->cache = $this->getCache( 'warehouse' );
	}


	/**
	 * Saves the product stock related data to the storage
	 *
	 * @param MShop_Product_Item_Interface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Interface $product, array $data )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'product/stock' );
		$manager->begin();

		try
		{
			$pos = 0;
			$delete = array();
			$map = $this->getMappedChunk( $data );
			$items = $this->getStockItems( $product->getId() );

			foreach( $map as $pos => $list )
			{
				if( !array_key_exists( 'product.stock.stocklevel', $list ) ) {
					continue;
				}

				$whcode = ( isset( $list['product.stock.warehouse'] ) ? $list['product.stock.warehouse'] : 'default' );

				if( !isset( $list['product.stock.warehouseid'] ) ) {
					$list['product.stock.warehouseid'] = $this->cache->get( $whcode );
				}

				if( isset( $list['product.stock.dateback'] ) && $list['product.stock.dateback'] === '' ) {
					$list['product.stock.dateback'] = null;
				}

				if( $list['product.stock.stocklevel'] === '' ) {
					$list['product.stock.stocklevel'] = null;
				}

				$list['product.stock.productid'] = $product->getId();

				if( ( $item = array_pop( $items ) ) === null ) {
					$item = $manager->createItem();
				}

				$item->fromArray( $list );
				$manager->saveItem( $item );
			}

			$manager->deleteItems( array_keys( $items ) );

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
	 * Returns the product properties for the given product ID
	 *
	 * @param string $prodid Unique product ID
	 * @return array Associative list of product stock items
	 */
	protected function getStockItems( $prodid )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'product/stock' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.productid', $prodid ) );

		return $manager->searchItems( $search );
	}
}
