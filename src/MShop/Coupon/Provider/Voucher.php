<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Voucher coupon model
 *
 * @package MShop
 * @subpackage Coupon
 */
class Voucher
	extends \Aimeos\MShop\Coupon\Provider\Factory\Base
	implements \Aimeos\MShop\Coupon\Provider\Iface, \Aimeos\MShop\Coupon\Provider\Factory\Iface
{
	private array $beConfig = array(
		'voucher.productcode' => array(
			'code' => 'voucher.productcode',
			'internalcode' => 'voucher.productcode',
			'label' => 'Product code of the rebate product',
			'default' => '',
			'required' => true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return $this->checkConfig( $this->beConfig, $attributes );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		$context = $this->context();

		if( ( $prodcode = $this->getConfigValue( 'voucher.productcode' ) ) === null )
		{
			$msg = $context->translate( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItem()->getProvider(), 'voucher.productcode' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'coupon/code' );
		$orderProductId = $manager->find( $this->getCode() )->getRef();

		$status = [\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED];
		$this->checkVoucher( (string) $orderProductId, $status );

		$orderProduct = $this->getOrderProductItem( (string) $orderProductId, (string) $order->getPrice()->getCurrencyId() );
		$value = $orderProduct->getPrice()->getValue() + $orderProduct->getPrice()->getRebate(); // @phpstan-ignore binaryOp.invalid
		$usedRebate = $this->getUsedRebate( $this->getCode(), $order->getId() );
		$rebate = $value - $usedRebate;

		if( $rebate <= 0 )
		{
			$msg = $context->translate( 'mshop', 'No more credit available for voucher "%1$s"' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, $this->getCode() ) );
		}

		$orderProducts = $this->createRebateProducts( $order, (string) $prodcode, $rebate );
		$orderProducts = $this->setOrderAttributeRebate( $orderProducts, $rebate );

		$order->setCoupon( $this->getCode(), $orderProducts );
		return $this;
	}


	/**
	 * Checks if the voucher for the given order product ID is still available
	 *
	 * @param string $orderProductId Order product ID of the bought voucher
	 * @param integer[] $status List of allowed payment status values
	 * @return void
	 * @throws \Aimeos\MShop\Coupon\Exception If voucher isn't available any more
	 */
	protected function checkVoucher( string $orderProductId, array $status ) : void
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'order' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.product.id', $orderProductId ),
			$search->compare( '==', 'order.statuspayment', $status ),
		];
		$search->setConditions( $search->and( $expr ) );

		if( $manager->search( $search )->isEmpty() )
		{
			$msg = $context->translate( 'mshop', 'No bought voucher for code "%1$s" available' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, $this->getCode() ) );
		}
	}


	/**
	 * Filters the order IDs and removes those order which aren't payed or recently created
	 *
	 * @param string[] $ids List of order IDs to check
	 * @param string|null $orderId ID of the current order which is excluded
	 * @return string[] List of filtered order IDs
	 */
	protected function filterOrderIds( array $ids, ?string $orderId = null ) : array
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'order' );

		$search = $manager->filter()->add( 'order.id', '==', $ids );
		$search->add( $this->redeemed( $search, $orderId ) );

		// @phpstan-ignore return.type
		return $manager->search( $search )->getId()->all();
	}


	/**
	 * Returns the ordered product item for the ID which is checked against the given currency
	 *
	 * @param string $orderProductId Unique ID of the ordered product
	 * @param string $currencyId Three letter ISO currecy code
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item
	 * @throws \Aimeos\MShop\Coupon\Exception If there's a mismatch between the currency IDs (order product vs. given one)
	 */
	protected function getOrderProductItem( string $orderProductId, string $currencyId ) : \Aimeos\MShop\Order\Item\Product\Iface
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'order/product' );

		$orderProduct = $manager->get( $orderProductId );
		$currency = $orderProduct->getPrice()->getCurrencyId();

		if( $currencyId !== $currency )
		{
			$msg = $context->translate( 'mshop', 'Bought voucher is in currency "%1$s", basket uses "%2$s"' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, (string) $currency, $currencyId ) );
		}

		return $orderProduct; // @phpstan-ignore return.type
	}


	/**
	 * Returns the already used rebate for the given voucher code
	 *
	 * @param string $code Voucher code
	 * @param string|null $orderId ID of the current order which is excluded
	 * @return float Already used rebate value
	 */
	protected function getUsedRebate( string $code, ?string $orderId = null ) : float
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, 'order/coupon' );

		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.coupon.code', $code ) );

		$orderIds = $prodIds = [];
		foreach( $manager->search( $search ) as $orderCouponItem )
		{
			$prodIds[] = $orderCouponItem->getProductId();
			$orderIds[] = $orderCouponItem->getParentId();
		}
		// @phpstan-ignore argument.type
		$orderIds = $this->filterOrderIds( $orderIds, $orderId );


		$manager = \Aimeos\MShop::create( $context, 'order/product' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.product.id', $prodIds ),
			$search->compare( '==', 'order.product.parentid', $orderIds ),
		];
		$search->setConditions( $search->and( $expr ) );

		$rebate = 0;
		foreach( $manager->search( $search ) as $orderProductItem ) {
			$rebate += $orderProductItem->getPrice()->getRebate(); // @phpstan-ignore assignOp.invalid
		}

		return $rebate;
	}


	/**
	 * Adds an attribute with the remaining rebate to the order products
	 *
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $orderProducts Order product items
	 * @param float $remaining Remaining rebate
	 * @return \Aimeos\MShop\Order\Item\Product\Iface[] Modified order product items
	 */
	protected function setOrderAttributeRebate( array $orderProducts, float $remaining ) : array
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'order/product/attribute' );

		$orderAttrItem = $manager->create();
		$orderAttrItem->setValue( number_format( $remaining, 2, '.', '' ) );
		$orderAttrItem->setCode( 'coupon-remain' );
		$orderAttrItem->setType( 'coupon' );

		foreach( $orderProducts as $orderProduct ) {
			// @phpstan-ignore argument.type
			$orderProduct->setAttributeItem( clone $orderAttrItem );
		}

		return $orderProducts;
	}
}
