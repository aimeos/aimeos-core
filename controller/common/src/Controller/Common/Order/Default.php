<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Common
 */


/**
 * Common order controller methods.
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Order_Default
	implements Controller_Common_Order_Interface
{
	private $_context;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
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
	 * @param MShop_Order_Item_Interface $orderItem Order item object
	 */
	public function block( MShop_Order_Item_Interface $orderItem )
	{
		$status = 1;
		$orderId = $orderItem->getId();


		$type = MShop_Order_Item_Status_Abstract::STOCK_UPDATE;

		if( ( $statusItem = $this->_getLastStatusItem( $orderItem->getId(), $type ) ) === false
			|| $statusItem->getValue() != $status
		) {
			$this->_updateStock( $orderItem, -1 );
			$this->_addStatusItem( $orderId, $type, $status );
		}


		$type = MShop_Order_Item_Status_Abstract::COUPON_UPDATE;

		if( ( $statusItem = $this->_getLastStatusItem( $orderItem->getId(), $type ) ) === false
			|| $statusItem->getValue() != $status
		) {
			$this->_updateCoupons( $orderItem, -1 );
			$this->_addStatusItem( $orderId, $type, $status );
		}
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
	 * @param MShop_Order_Item_Interface $orderItem Order item object
	 */
	public function unblock( MShop_Order_Item_Interface $orderItem )
	{
		$status = 0;
		$orderId = $orderItem->getId();


		$type = MShop_Order_Item_Status_Abstract::STOCK_UPDATE;

		if( ( $statusItem = $this->_getLastStatusItem( $orderItem->getId(), $type ) ) === false
			|| $statusItem->getValue() != $status
		) {
			$this->_updateStock( $orderItem, +1 );
			$this->_addStatusItem( $orderId, $type, $status );
		}


		$type = MShop_Order_Item_Status_Abstract::COUPON_UPDATE;

		if( ( $statusItem = $this->_getLastStatusItem( $orderItem->getId(), $type ) ) === false
			|| $statusItem->getValue() != $status
		) {
			$this->_updateCoupons( $orderItem, +1 );
			$this->_addStatusItem( $orderId, $type, $status );
		}
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
	 * @param MShop_Order_Item_Interface $orderItem Order item object
	 */
	public function update( MShop_Order_Item_Interface $orderItem )
	{
		switch( $orderItem->getPaymentStatus() )
		{
			case MShop_Order_Item_Abstract::PAY_DELETED:
			case MShop_Order_Item_Abstract::PAY_CANCELED:
			case MShop_Order_Item_Abstract::PAY_REFUSED:
			case MShop_Order_Item_Abstract::PAY_REFUND:
				$this->unblock( $orderItem );
				break;

			case MShop_Order_Item_Abstract::PAY_PENDING:
			case MShop_Order_Item_Abstract::PAY_AUTHORIZED:
			case MShop_Order_Item_Abstract::PAY_RECEIVED:
				$this->block( $orderItem );
				break;
		}
	}


	protected function _addStatusItem( $parentid, $type, $value )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'order/status' );

		$item = $manager->createItem();
		$item->setParentId( $parentid );
		$item->setType( $type );
		$item->setValue( $value );

		$manager->saveItem( $item );
	}


	protected function _getContext()
	{
		return $this->_context;
	}


	protected function _getLastStatusItem( $parentid, $type )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'order/status' );

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


	protected function _updateCoupons( MShop_Order_Item_Interface $orderItem, $how = +1 )
	{
		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'order/base/coupon' );
		$couponCodeManager = MShop_Factory::createManager( $context, 'coupon/code' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.coupon.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$couponCodeManager->begin();

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


	protected function _updateStock( MShop_Order_Item_Interface $orderItem, $how = +1 )
	{
		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'order/base/product' );
		$stockManager = MShop_Factory::createManager( $context, 'product/stock' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.baseid', $orderItem->getBaseId() ) );

		$start = 0;

		$stockManager->begin();

		do
		{
			$items = $manager->searchItems( $search );

			foreach( $items as $item ) {
				$stockManager->increase( $item->getProductCode(), $item->getWarehouseCode(), $how * $item->getQuantity() );
			}

			$count = count( $items );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );

		$stockManager->commit();
	}
}
