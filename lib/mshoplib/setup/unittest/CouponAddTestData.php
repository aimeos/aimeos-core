<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds the coupon test data.
 */
class MW_Setup_Task_CouponAddTestData extends MW_Setup_Task_Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop', 'MShopSetLocale', 'OrderAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds coupon test data.
	 */
	protected function process()
	{
		$this->msg( 'Adding coupon test data', 0 );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'coupon.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for coupon test data', $path ) );
		}

		$this->addCouponData( $testdata );
		$this->addOrderCouponTestData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the coupon test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addCouponData( array $testdata )
	{
		$couponManager = MShop_Coupon_Manager_Factory::createManager( $this->additional, 'Default' );
		$couponCodeManager = $couponManager->getSubmanager( 'code' );

		$couponIds = array();
		$coupon = $couponManager->createItem();
		foreach( $testdata['coupon'] as $key => $dataset )
		{
			$coupon->setId( null );
			$coupon->setLabel( $dataset['label'] );
			$coupon->setProvider( $dataset['provider'] );
			$coupon->setDateStart( $dataset['start'] );
			$coupon->setDateEnd( $dataset['end'] );
			$coupon->setConfig( $dataset['config'] );
			$coupon->setStatus( $dataset['status'] );

			$couponManager->saveItem( $coupon );
			$couponIds[$key] = $coupon->getId();
		}


		$ccode = $couponCodeManager->createItem();
		foreach( $testdata['coupon/code'] as $key => $dataset )
		{
			if( !isset( $couponIds[$dataset['couponid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No coupon ID found for "%1$s"', $dataset['couponid'] ) );
			}

			$ccode->setId( null );
			$ccode->setCouponId( $couponIds[$dataset['couponid']] );
			$ccode->setCount( $dataset['count'] );
			$ccode->setDateStart( $dataset['start'] );
			$ccode->setDateEnd( $dataset['end'] );
			$ccode->setCode( $dataset['code'] );

			$couponCodeManager->saveItem( $ccode, false );
		}
	}


	/**
	 * Adds the order coupon test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addOrderCouponTestData( array $testdata )
	{
		$order = MShop_Order_Manager_Factory::createManager( $this->additional, 'Default' );
		$orderBase = $order->getSubManager( 'base', 'Default' );
		$orderBaseProd = $orderBase->getSubManager( 'product', 'Default' );
		$orderBaseCoupon = $orderBase->getSubManager( 'coupon', 'Default' );

		$orderBaseIds = array();
		$orderBasePrices = array();
		$ordProdIds = array();
		$prodcode = $quantity = $pos = array();
		foreach( $testdata['order/base/coupon'] as $key => $dataset ) {
			$exp = explode( '/', $dataset['ordprodid'] );

			if( count( $exp ) != 3 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ordprod are set wrong "%1$s"', $dataset ) );
			}

			$prodcode[$exp[0]] = $exp[0];
			$quantity[$exp[1]] = $exp[1];
			$pos[$exp[2]] = $exp[2];

			$orderBasePrices[$dataset['baseid']] = $dataset['baseid'];
		}

		$search = $orderBase->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', $orderBasePrices ) );

		foreach( $orderBase->searchItems( $search ) as $orderBaseItem ) {
			$orderBaseIds[$orderBaseItem->getPrice()->getValue()] = $orderBaseItem->getId();
		}


		$search = $orderBaseProd->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.product.prodcode', $prodcode ),
			$search->compare( '==', 'order.base.product.quantity', $quantity ),
			$search->compare( '==', 'order.base.product.position', $pos ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $orderBaseProd->searchItems( $search ) as $ordProd ) {
			$ordProdIds[$ordProd->getProductCode() . '/' . $ordProd->getQuantity() . '/' . $ordProd->getPosition()] = $ordProd->getId();
		}

		$orderCoupon = $orderBaseCoupon->createItem();
		foreach( $testdata['order/base/coupon'] as $key => $dataset )
		{
			if( !isset( $orderBaseIds[$dataset['baseid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No oder base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			if( !isset( $ordProdIds[$dataset['ordprodid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No order base product ID found for "%1$s"', $dataset['ordprodid'] ) );
			}

			$orderCoupon->setId( null );
			$orderCoupon->setBaseId( $orderBaseIds[$dataset['baseid']] );
			$orderCoupon->setProductId( $ordProdIds[$dataset['ordprodid']] );
			$orderCoupon->setCode( $dataset['code'] );

			$orderBaseCoupon->saveItem( $orderCoupon, false );
		}
	}
}