<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Common
 */


/**
 * Common order controller methods.
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Order_Standard
	implements Controller_Common_Order_Iface
{
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Iface $context
	 */
	public function __construct( MShop_Context_Item_Iface $context )
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
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 */
	public function block( MShop_Order_Item_Iface $orderItem )
	{
		$this->updateStatus( $orderItem, MShop_Order_Item_Status_Base::STOCK_UPDATE, 1, -1 );
		$this->updateStatus( $orderItem, MShop_Order_Item_Status_Base::COUPON_UPDATE, 1, -1 );
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
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 */
	public function unblock( MShop_Order_Item_Iface $orderItem )
	{
		$this->updateStatus( $orderItem, MShop_Order_Item_Status_Base::STOCK_UPDATE, 0, +1 );
		$this->updateStatus( $orderItem, MShop_Order_Item_Status_Base::COUPON_UPDATE, 0, +1 );
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
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 */
	public function update( MShop_Order_Item_Iface $orderItem )
	{
		switch( $orderItem->getPaymentStatus() )
		{
			case MShop_Order_Item_Base::PAY_DELETED:
			case MShop_Order_Item_Base::PAY_CANCELED:
			case MShop_Order_Item_Base::PAY_REFUSED:
			case MShop_Order_Item_Base::PAY_REFUND:
				$this->unblock( $orderItem );
				break;

			case MShop_Order_Item_Base::PAY_PENDING:
			case MShop_Order_Item_Base::PAY_AUTHORIZED:
			case MShop_Order_Item_Base::PAY_RECEIVED:
				$this->block( $orderItem );
				break;
		}
	}


	/**
	 * Adds a new status record to the order with the type and value.
	 *
	 * @param string $parentid Order ID
	 * @param string $type Status type
	 * @param string $value Status value
	 */
	protected function addStatusItem( $parentid, $type, $value )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'order/status' );

		$item = $manager->createItem();
		$item->setParentId( $parentid );
		$item->setType( $type );
		$item->setValue( $value );

		$manager->saveItem( $item );
	}


	/**
	 * Returns the context item object.
	 *
	 * @return MShop_Context_Item_Iface Context item object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the last status item for the given order ID.
	 *
	 * @param string $parentid Order ID
	 * @return MShop_Order_Item_Status_Iface|false Order status item or false if no item is available
	 */
	protected function getLastStatusItem( $parentid, $type )
	{
		$manager = MShop_Factory::createManager( $this->getContext(), 'order/status' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.status.parentid', $parentid ),
			$search->compare( '==', 'order.status.type', $type ),
			$search->compare( '!=', 'order.status.value', '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '-', 'order.status.ctime' ) ) );
		$search->setSlice( 0, 1 );

		$result = $manager->searchItems( $search );

		return reset( $result );
	}


	/**
	 * Increases or decreses the coupon code counts referenced in the order by the given value.
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @param integer $how Positive or negative integer number for increasing or decreasing the coupon count
	 */
	protected function updateCoupons( MShop_Order_Item_Iface $orderItem, $how = +1 )
	{
		$context = $this->getContext();
		$manager = MShop_Factory::createManager( $context, 'order/base/coupon' );
		$couponCodeManager = MShop_Factory::createManager( $context, 'coupon/code' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.coupon.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$couponCodeManager->begin();

		try
		{
			do
			{
				$items = $manager->searchItems( $search );

				foreach( $items as $item ) {
					$couponCodeManager->increase( $item->getCode(), $how * 1 );
				}

				$count = count( $items );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );

			$couponCodeManager->commit();
		}
		catch( Exception $e )
		{
			$couponCodeManager->rollback();
			throw $e;
		}
	}


	/**
	 * Increases or decreases the stock level or the coupon code count for referenced items of the given order.
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @param string $type Constant from MShop_Order_Item_Status_Base, e.g. STOCK_UPDATE or COUPON_UPDATE
	 * @param string $status New status value stored along with the order item
	 * @param integer $value Number to increse or decrease the stock level or coupon code count
	 */
	protected function updateStatus( MShop_Order_Item_Iface $orderItem, $type, $status, $value )
	{
		$statusItem = $this->getLastStatusItem( $orderItem->getId(), $type );

		if( $statusItem !== false && $statusItem->getValue() == $status ) {
			return;
		}

		if( $type == MShop_Order_Item_Status_Base::STOCK_UPDATE ) {
			$this->updateStock( $orderItem, $value );
		} elseif( $type == MShop_Order_Item_Status_Base::COUPON_UPDATE ) {
			$this->updateCoupons( $orderItem, $value );
		}

		$this->addStatusItem( $orderItem->getId(), $type, $status );
	}


	/**
	 * Increases or decreses the stock levels of the products referenced in the order by the given value.
	 *
	 * @param MShop_Order_Item_Iface $orderItem Order item object
	 * @param integer $how Positive or negative integer number for increasing or decreasing the stock levels
	 */
	protected function updateStock( MShop_Order_Item_Iface $orderItem, $how = +1 )
	{
		$context = $this->getContext();
		$productManager = MShop_Factory::createManager( $context, 'product' );
		$stockManager = MShop_Factory::createManager( $context, 'product/stock' );
		$manager = MShop_Factory::createManager( $context, 'order/base/product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$stockManager->begin();

		try
		{
			do
			{
				$items = $manager->searchItems( $search );

				foreach( $items as $item )
				{
					$stockManager->increase( $item->getProductCode(), $item->getWarehouseCode(), $how * $item->getQuantity() );

					// recalculate stock level of product bundles
					$search = $productManager->createSearch();
					$expr = array(
						$search->compare( '==', 'product.type.code', 'bundle' ),
						$search->compare( '==', 'product.lists.domain', 'product' ),
						$search->compare( '==', 'product.lists.refid', $item->getProductId() ),
						$search->compare( '==', 'product.lists.type.code', 'default' ),
					);
					$search->setConditions( $search->combine( '&&', $expr ) );
					$search->setSlice( 0, 0x7fffffff );

					$bundleItems = $productManager->searchItems( $search, array( 'product' ) );

					$this->updateStockBundle( $bundleItems, $item->getWarehouseCode() );
				}

				$count = count( $items );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );

			$stockManager->commit();
		}
		catch( Exception $e )
		{
			$stockManager->rollback();
			throw $e;
		}
	}


	/**
	 * Updates the stock levels of bundles for a specific warehouse
	 *
	 * @param array $bundleItems List of items implementing MShop_Product_Item_Iface
	 * @param string $whcode Unique warehouse code
	 */
	protected function updateStockBundle( array $bundleItems, $whcode )
	{
		$bundleMap = $prodIds = $stock = array();
		$stockManager = MShop_Factory::createManager( $this->getContext(), 'product/stock' );


		foreach( $bundleItems as $bundleId => $bundleItem )
		{
			foreach( $bundleItem->getRefItems( 'product', null, 'default' ) as $id => $item )
			{
				$bundleMap[$id][] = $bundleId;
				$prodIds[] = $id;
			}
		}

		if( empty( $prodIds ) ) {
			return;
		}


		$search = $stockManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.stock.productid', $prodIds ),
			$search->compare( '==', 'product.stock.warehouse.code', $whcode ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		foreach( $stockManager->searchItems( $search ) as $stockItem )
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
				}
			}
		}


		if( empty( $stock ) ) {
			return;
		}

		$search = $stockManager->createSearch();
		$expr = array(
				$search->compare( '==', 'product.stock.productid', array_keys( $bundleItems ) ),
				$search->compare( '==', 'product.stock.warehouse.code', $whcode ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		foreach( $stockManager->searchItems( $search ) as $item )
		{
			if( isset( $stock[$item->getProductId()] ) )
			{
				$item->setStockLevel( $stock[$item->getProductId()] );
				$stockManager->saveItem( $item );
			}
		}
	}
}
