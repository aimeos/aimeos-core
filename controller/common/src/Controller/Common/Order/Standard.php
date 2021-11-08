<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Order;


/**
 * Common order controller methods.
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	implements \Aimeos\Controller\Common\Order\Iface
{
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Blocks the resources listed in the order.
	 *
	 * Every order contains resources like products or redeemed coupon codes
	 * that must be blocked so they can't be used by another customer in a
	 * later order. This method reduces the the stock level of products, the
	 * counts of coupon codes and others.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the resources haven't been unblocked in the meantime.
	 *
	 * You can also block and unblock resources several times. Please keep in
	 * mind that unblocked resources may be reused by other orders in the
	 * meantime. This can lead to an oversell of products!
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @return \Aimeos\MShop\Order\Item\Iface Order item object
	 */
	public function block( \Aimeos\MShop\Order\Item\Iface $orderItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->updateStatus( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE, 1, -1 );
		$this->updateStatus( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE, 1, -1 );

		return $orderItem;
	}


	/**
	 * Frees the resources listed in the order.
	 *
	 * If customers created orders but didn't pay for them, the blocked resources
	 * like products and redeemed coupon codes must be unblocked so they can be
	 * ordered again or used by other customers. This method increased the stock
	 * level of products, the counts of coupon codes and others.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the resources haven't been blocked in the meantime.
	 *
	 * You can also unblock and block resources several times. Please keep in
	 * mind that unblocked resources may be reused by other orders in the
	 * meantime. This can lead to an oversell of products!
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @return \Aimeos\MShop\Order\Item\Iface Order item object
	 */
	public function unblock( \Aimeos\MShop\Order\Item\Iface $orderItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->updateStatus( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE, 0, +1 );
		$this->updateStatus( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE, 0, +1 );

		return $orderItem;
	}


	/**
	 * Blocks or frees the resources listed in the order if necessary.
	 *
	 * After payment status updates, the resources like products or coupon
	 * codes listed in the order must be blocked or unblocked. This method
	 * cares about executing the appropriate action depending on the payment
	 * status.
	 *
	 * It's save to call this method multiple times for one order. In this case,
	 * the actions will be executed only once. All subsequent calls will do
	 * nothing as long as the payment status hasn't changed in the meantime.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @return \Aimeos\MShop\Order\Item\Iface Order item object
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $orderItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		switch( $orderItem->getStatusPayment() )
		{
			case \Aimeos\MShop\Order\Item\Base::PAY_DELETED:
			case \Aimeos\MShop\Order\Item\Base::PAY_CANCELED:
			case \Aimeos\MShop\Order\Item\Base::PAY_REFUSED:
			case \Aimeos\MShop\Order\Item\Base::PAY_REFUND:
				$this->unblock( $orderItem );
				break;

			case \Aimeos\MShop\Order\Item\Base::PAY_PENDING:
			case \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED:
			case \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED:
				$this->block( $orderItem );
				break;
		}

		return $orderItem;
	}


	/**
	 * Adds a new status record to the order with the type and value.
	 *
	 * @param string $parentid Order ID
	 * @param string $type Status type
	 * @param string $value Status value
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function addStatusItem( string $parentid, string $type, string $value ) : Iface
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/status' );

		$item = $manager->create();
		$item->setParentId( $parentid );
		$item->setType( $type );
		$item->setValue( $value );

		$manager->save( $item, false );

		return $this;
	}


	/**
	 * Returns the product articles and their bundle product codes for the given article ID
	 *
	 * @param string $prodId Product ID of the article whose stock level changed
	 * @return array Associative list of article codes as keys and lists of bundle product codes as values
	 */
	protected function getBundleMap( string $prodId ) : array
	{
		$bundleMap = [];
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );

		$search = $productManager->filter();
		$func = $search->make( 'product:has', ['product', 'default', $prodId] );
		$expr = array(
			$search->compare( '==', 'product.type', 'bundle' ),
			$search->compare( '!=', $func, null ),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 0x7fffffff );

		$bundleItems = $productManager->search( $search, array( 'product' ) );

		foreach( $bundleItems as $bundleItem )
		{
			foreach( $bundleItem->getRefItems( 'product', null, 'default' ) as $item ) {
				$bundleMap[$item->getId()][] = $bundleItem->getId();
			}
		}

		return $bundleMap;
	}


	/**
	 * Returns the context item object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the last status item for the given order ID.
	 *
	 * @param string $parentid Order ID
	 * @param string $type Status type constant
	 * @param string $status New status value stored along with the order item
	 * @return \Aimeos\MShop\Order\Item\Status\Iface|null Order status item or NULL if no item is available
	 */
	protected function getLastStatusItem( string $parentid, string $type, string $status ) : ?\Aimeos\MShop\Order\Item\Status\Iface
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/status' );

		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', 'order.status.parentid', $parentid ),
			$search->compare( '==', 'order.status.type', $type ),
			$search->compare( '==', 'order.status.value', $status ),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->setSortations( array( $search->sort( '-', 'order.status.ctime' ) ) );
		$search->slice( 0, 1 );

		return $manager->search( $search )->first();
	}


	/**
	 * Returns the stock items for the given product codes
	 *
	 * @param iterable $prodIds List of product codes
	 * @param string $stockType Stock type code the stock items must belong to
	 * @return \Aimeos\Map Associative list of \Aimeos\MShop\Stock\Item\Iface and IDs as values
	 */
	protected function getStockItems( iterable $prodIds, string $stockType ) : \Aimeos\Map
	{
		$stockManager = \Aimeos\MShop::create( $this->context, 'stock' );

		$search = $stockManager->filter()->slice( 0, count( $prodIds ) )
			->add( ['stock.productid' => $prodIds, 'stock.type' => $stockType] );

		return $stockManager->search( $search );
	}


	/**
	 * Increases or decreses the coupon code counts referenced in the order by the given value.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @param int $how Positive or negative integer number for increasing or decreasing the coupon count
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function updateCoupons( \Aimeos\MShop\Order\Item\Iface $orderItem, int $how = +1 )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'order/base/coupon' );
		$couponCodeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.coupon.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$couponCodeManager->begin();

		try
		{
			do
			{
				$items = $manager->search( $search );

				foreach( $items as $item ) {
					$couponCodeManager->decrease( $item->getCode(), $how * -1 );
				}

				$count = count( $items );
				$start += $count;
				$search->slice( $start );
			}
			while( $count >= $search->getLimit() );

			$couponCodeManager->commit();
		}
		catch( \Exception $e )
		{
			$couponCodeManager->rollback();
			throw $e;
		}

		return $this;
	}


	/**
	 * Increases or decreases the stock level or the coupon code count for referenced items of the given order.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @param string $type Constant from \Aimeos\MShop\Order\Item\Status\Base, e.g. STOCK_UPDATE or COUPON_UPDATE
	 * @param string $status New status value stored along with the order item
	 * @param int $value Number to increse or decrease the stock level or coupon code count
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function updateStatus( \Aimeos\MShop\Order\Item\Iface $orderItem, string $type, string $status, int $value )
	{
		$statusItem = $this->getLastStatusItem( $orderItem->getId(), $type, $status );

		if( $statusItem && $statusItem->getValue() == $status ) {
			return;
		}

		if( $type == \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE ) {
			$this->updateStock( $orderItem, $value );
		} elseif( $type == \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE ) {
			$this->updateCoupons( $orderItem, $value );
		}

		return $this->addStatusItem( $orderItem->getId(), $type, $status );
	}


	/**
	 * Increases or decreses the stock levels of the products referenced in the order by the given value.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $orderItem Order item object
	 * @param int $how Positive or negative integer number for increasing or decreasing the stock levels
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function updateStock( \Aimeos\MShop\Order\Item\Iface $orderItem, int $how = +1 )
	{
		$context = $this->getContext();
		$stockManager = \Aimeos\MShop::create( $context, 'stock' );
		$manager = \Aimeos\MShop::create( $context, 'order/base/product' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$stockManager->begin();

		try
		{
			do
			{
				$items = $manager->search( $search );

				foreach( $items as $item )
				{
					$stockManager->decrease( [$item->getProductId() => -1 * $how * $item->getQuantity()], $item->getStockType() );

					switch( $item->getType() ) {
						case 'default':
							$this->updateStockBundle( $item->getParentProductId(), $item->getStockType() ); break;
						case 'select':
							$this->updateStockSelection( $item->getParentProductId(), $item->getStockType() ); break;
					}
				}

				$count = count( $items );
				$start += $count;
				$search->slice( $start );
			}
			while( $count >= $search->getLimit() );

			$stockManager->commit();
		}
		catch( \Exception $e )
		{
			$stockManager->rollback();
			throw $e;
		}

		return $this;
	}


	/**
	 * Updates the stock levels of bundles for a specific type
	 *
	 * @param string $prodId Unique product ID
	 * @param string $stockType Unique stock type
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function updateStockBundle( string $prodId, string $stockType )
	{
		if( ( $bundleMap = $this->getBundleMap( $prodId ) ) === [] ) {
			return;
		}


		$bundleIds = $stock = [];

		foreach( $this->getStockItems( array_keys( $bundleMap ), $stockType ) as $stockItem )
		{
			if( isset( $bundleMap[$stockItem->getProductId()] ) && $stockItem->getStockLevel() !== null )
			{
				foreach( $bundleMap[$stockItem->getProductId()] as $bundleId )
				{
					if( isset( $stock[$bundleId] ) ) {
						$stock[$bundleId] = min( $stock[$bundleId], $stockItem->getStockLevel() );
					} else {
						$stock[$bundleId] = $stockItem->getStockLevel();
					}

					$bundleIds[$bundleId] = null;
				}
			}
		}

		if( empty( $stock ) ) {
			return;
		}

		$stockManager = \Aimeos\MShop::create( $this->context, 'stock' );

		foreach( $this->getStockItems( array_keys( $bundleIds ), $stockType ) as $item )
		{
			if( isset( $stock[$item->getProductId()] ) )
			{
				$item->setStockLevel( $stock[$item->getProductId()] );
				$stockManager->save( $item );
			}
		}

		return $this;
	}


	/**
	 * Updates the stock levels of selection products for a specific type
	 *
	 * @param string $prodId Unique product ID
	 * @param string $stocktype Unique stock type
	 * @return \Aimeos\Controller\Common\Order\Iface Order controller for fluent interface
	 */
	protected function updateStockSelection( string $prodId, string $stocktype )
	{
		$stockManager = \Aimeos\MShop::create( $this->context, 'stock' );
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );

		$productItem = $productManager->get( $prodId, ['product'] );
		$prodIds = $productItem->getRefItems( 'product', 'default', 'default' )->getId()->push( $productItem->getId() );

		$stockItems = $this->getStockItems( $prodIds, $stocktype )->col( null, 'stock.productid' );
		$selStockItem = $stockItems->pull( $prodId ) ?: $stockManager->create();

		$sum = $stockItems->getStockLevel()->reduce( function( $result, $value ) {
			return $result !== null && $value !== null ? $result + $value : null;
		}, 0 );

		$selStockItem->setProductId( $productItem->getId() )->setType( $stocktype )->setStockLevel( $sum );
		$stockManager->save( $selStockItem, false );

		return $this;
	}
}
