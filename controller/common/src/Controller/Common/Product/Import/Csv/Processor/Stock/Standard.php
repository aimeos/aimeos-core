<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Stock;


/**
 * Product stock processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Processor\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface
{
	/** controller/common/product/import/csv/processor/stock/name
	 * Name of the stock processor implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Controller\Common\Product\Import\Csv\Processor\Stock\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the processor class name
	 * @since 2015.10
	 * @category Developer
	 */

	private $cache;


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

		$this->cache = $this->getCache( 'warehouse' );
	}


	/**
	 * Saves the product stock related data to the storage
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( \Aimeos\MShop\Product\Item\Iface $product, array $data )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/stock' );
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

				$list['product.stock.parentid'] = $product->getId();

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
		catch( \Exception $e )
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
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/stock' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.parentid', $prodid ) );

		return $manager->searchItems( $search );
	}
}
