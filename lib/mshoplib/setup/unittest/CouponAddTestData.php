<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds the coupon test data.
 */
class CouponAddTestData extends \Aimeos\MW\Setup\Task\Base
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
	 * Adds coupon test data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding coupon test data', 0 );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'coupon.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for coupon test data', $path ) );
		}

		$this->addCouponData( $testdata );
		$this->addOrderCouponTestData( $testdata );

		$this->status( 'done' );
	}


	/**
	 * Adds the coupon test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addCouponData( array $testdata )
	{
		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $this->additional, 'Standard' );
		$couponCodeManager = $couponManager->getSubmanager( 'code' );

		$couponIds = [];
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
			if( !isset( $couponIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No coupon ID found for "%1$s"', $dataset['parentid'] ) );
			}

			$ccode->setId( null );
			$ccode->setParentId( $couponIds[$dataset['parentid']] );
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
	 * @throws \Aimeos\MW\Setup\Exception If a required ID is not available
	 */
	private function addOrderCouponTestData( array $testdata )
	{
		$order = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->additional, 'Standard' );
		$orderBase = $order->getSubManager( 'base', 'Standard' );
		$orderBaseProd = $orderBase->getSubManager( 'product', 'Standard' );
		$orderBaseCoupon = $orderBase->getSubManager( 'coupon', 'Standard' );

		$orderBaseIds = [];
		$orderBasePrices = [];
		$ordProdIds = [];
		$prodcode = $quantity = $pos = [];
		foreach( $testdata['order/base/coupon'] as $key => $dataset ) {
			$exp = explode( '/', $dataset['ordprodid'] );

			if( count( $exp ) != 3 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ordprod are set wrong "%1$s"', $dataset ) );
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
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No oder base ID found for "%1$s"', $dataset['baseid'] ) );
			}

			if( !isset( $ordProdIds[$dataset['ordprodid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No order base product ID found for "%1$s"', $dataset['ordprodid'] ) );
			}

			$orderCoupon->setId( null );
			$orderCoupon->setBaseId( $orderBaseIds[$dataset['baseid']] );
			$orderCoupon->setProductId( $ordProdIds[$dataset['ordprodid']] );
			$orderCoupon->setCode( $dataset['code'] );

			$orderBaseCoupon->saveItem( $orderCoupon, false );
		}
	}
}