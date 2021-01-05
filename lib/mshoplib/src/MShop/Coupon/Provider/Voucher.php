<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	private $beConfig = array(
		'voucher.productcode' => array(
			'code' => 'voucher.productcode',
			'internalcode' => 'voucher.productcode',
			'label' => 'Product code of the rebate product',
			'type' => 'string',
			'internaltype' => 'string',
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
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Provider object for method chaining
	 */
	public function update( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		$context = $this->getContext();

		if( ( $prodcode = $this->getConfigValue( 'voucher.productcode' ) ) === null )
		{
			$msg = $context->getI18n()->dt( 'mshop', 'Invalid configuration for coupon provider "%1$s", needs "%2$s"' );
			$msg = sprintf( $msg, $this->getItem()->getProvider(), 'voucher.productcode' );
			throw new \Aimeos\MShop\Coupon\Exception( $msg );
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'coupon/code' );
		$orderProductId = $manager->find( $this->getCode() )->getRef();

		$status = [\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED];
		$this->checkVoucher( $orderProductId, $status );

		$orderProduct = $this->getOrderProductItem( $orderProductId, $base->getPrice()->getCurrencyId() );
		$value = $orderProduct->getPrice()->getValue() + $orderProduct->getPrice()->getRebate();
		$usedRebate = $this->getUsedRebate( $this->getCode() );
		$rebate = $value - $usedRebate;

		if( $rebate <= 0 )
		{
			$msg = $context->getI18n()->dt( 'mshop', 'No more credit available for voucher "%1$s"' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, $this->getCode() ) );
		}

		$orderProducts = $this->createRebateProducts( $base, $prodcode, $rebate );
		$orderProducts = $this->setOrderAttributeRebate( $orderProducts, $rebate );

		$base->setCoupon( $this->getCode(), $orderProducts );
		return $this;
	}


	/**
	 * Checks if the voucher for the given order product ID is still available
	 *
	 * @param string $orderProductId Order product ID of the bought voucher
	 * @param integer[] $status List of allowed payment status values
	 * @throws \Aimeos\MShop\Coupon\Exception If voucher isn't available any more
	 */
	protected function checkVoucher( string $orderProductId, array $status )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'order' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.base.product.id', $orderProductId ),
			$search->compare( '==', 'order.statuspayment', $status ),
		];
		$search->setConditions( $search->and( $expr ) );

		if( $manager->search( $search )->isEmpty() )
		{
			$msg = $context->getI18n()->dt( 'mshop', 'No bought voucher for code "%1$s" available' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, $this->getCode() ) );
		}
	}


	/**
	 * Filters the order base IDs and removes those order which aren't payed
	 *
	 * @param string[] $baseIds List of order base IDs to check
	 * @return string[] List of filtered order base IDs
	 */
	protected function filterOrderBaseIds( array $baseIds ) : array
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.baseid', $baseIds ),
			$search->compare( '>=', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_PENDING ),
		];
		$search->setConditions( $search->and( $expr ) );

		$list = [];
		foreach( $manager->search( $search ) as $orderItem ) {
			$list[] = $orderItem->getBaseId();
		}

		return $list;
	}


	/**
	 * Returns the ordered product item for the ID which is checked against the given currency
	 *
	 * @param string $orderProductId Unique ID of the ordered product
	 * @param string $currencyId Three letter ISO currecy code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 * @throws \Aimeos\MShop\Coupon\Exception If there's a mismatch between the currency IDs (order product vs. given one)
	 */
	protected function getOrderProductItem( string $orderProductId, string $currencyId ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'order/base/product' );

		$orderProduct = $manager->get( $orderProductId );
		$currency = $orderProduct->getPrice()->getCurrencyId();

		if( $currencyId !== $currency )
		{
			$msg = $context->getI18n()->dt( 'mshop', 'Bought voucher is in currency "%1$s", basket uses "%2$s"' );
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( $msg, $currency, $currencyId ) );
		}

		return $orderProduct;
	}


	/**
	 * Returns the already used rebate for the given voucher code
	 *
	 * @param string $code Voucher code
	 * @return float Already used rebate value
	 */
	protected function getUsedRebate( string $code ) : float
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'order/base/coupon' );

		$search = $manager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.base.coupon.code', $code ) );

		$baseIds = $prodIds = [];
		foreach( $manager->search( $search ) as $orderCouponItem )
		{
			$prodIds[] = $orderCouponItem->getProductId();
			$baseIds[] = $orderCouponItem->getBaseId();
		}
		$baseIds = $this->filterOrderBaseIds( $baseIds );



		$manager = \Aimeos\MShop::create( $context, 'order/base/product' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.base.product.id', $prodIds ),
			$search->compare( '==', 'order.base.product.baseid', $baseIds ),
		];
		$search->setConditions( $search->and( $expr ) );

		$rebate = 0;
		foreach( $manager->search( $search ) as $orderProductItem ) {
			$rebate += $orderProductItem->getPrice()->getRebate();
		}

		return $rebate;
	}


	/**
	 * Adds an attribute with the remaining rebate to the order products
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $orderProducts Order product items
	 * @param float $remaining Remaining rebate
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] Modified order product items
	 */
	protected function setOrderAttributeRebate( array $orderProducts, float $remaining ) : array
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/base/product/attribute' );

		$orderAttrItem = $manager->create();
		$orderAttrItem->setValue( number_format( $remaining, 2, '.', '' ) );
		$orderAttrItem->setCode( 'coupon-remain' );
		$orderAttrItem->setType( 'coupon' );

		foreach( $orderProducts as $orderProduct ) {
			$orderProduct->setAttributeItem( clone $orderAttrItem );
		}

		return $orderProducts;
	}
}
