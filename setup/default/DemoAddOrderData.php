<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to order tables.
 */
class DemoAddOrderData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Customer', 'Coupon', 'Order', 'Product', 'Serivce'];
	}


	/**
	 * Insert order data.
	 */
	public function up()
	{
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing order demo data', 'vv' );

		$manager = \Aimeos\MShop::create( $context, 'order' );
		$customer = \Aimeos\MShop::create( $context, 'customer' )->find( 'demo@example.com' );

		$filter = $manager->filter();
		$filter->add( 'order.customerid', '==', $customer->getId() );
		$items = $manager->search( $filter );

		$manager->delete( $items );


		if( $value === '1' ) {
			$this->add( $customer );
		}
	}


	/**
	 * Adds the demo orders.
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $customer Customer item object
	 */
	protected function add( \Aimeos\MShop\Customer\Item\Iface $customer )
	{
		$paystatus = [
			\Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
			\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED,
			\Aimeos\MShop\Order\Item\Base::PAY_PENDING
		];

		foreach( $this->locales() as $locale )
		{
			$lcontext = clone $this->context();
			$lcontext->setLocale( $locale );

			$products = $this->products( $lcontext );
			$services = $this->services( $lcontext );

			$manager = \Aimeos\MShop::create( $lcontext, 'order' );
			$manager->begin();

			for( $i = 0; $i < 10; $i++ )
			{
				for( $j = 0; $j < floor( $i / 4 ) + 1; $j++ )
				{
					$item = $manager->create();
					$item->setCustomerId( $customer->getId() );
					$item->setStatusPayment( $paystatus[( $i + $j ) % 3] );
					$item->setDatePayment( date( 'Y-m-d H:i:s', time() + ( -10 + $i ) * ( 66400 + $j * 10000 ) ) );
					$item->setLocale( $locale );

					$item->addAddress( $this->address( $customer->getPaymentAddress() ), 'payment' );

					foreach( $products->random( rand( 1, 2 ) ) as $product ) {
						$item->addProduct( $product );
					}

					foreach( map( $services->get( 'delivery', [] ) )->random( 1 ) as $service ) {
						$item->addService( $service, 'delivery' );
					}

					foreach( map( $services->get( 'payment', [] ) )->random( 1 ) as $service ) {
						$item->addService( $service, 'payment' );
					}

					$manager->save( $item );
				}
			}

			$manager->commit();
		}
	}


	protected function address( \Aimeos\MShop\Common\Item\Address\Standard $address ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'order/address' );
		$firstname = ['John', 'Jane', 'Max', 'Anna', 'Tom', 'Emma', 'Peter', 'Lucy', 'Paul', 'Lily'];
		$lastname = ['Doe', 'Smith', 'Miller', 'Brown', 'Taylor', 'Wilson', 'Evans', 'Singh', 'Li', 'Müller'];

		return $manager->create()->copyFrom( $address )->setFirstname( $firstname[rand( 0, 9 )] )->setLastname( $lastname[rand( 0, 9 )] );
	}


	protected function locales() : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'locale' );
		$items = $manager->search( $manager->filter() );
		$list = [];

		foreach( $items as $item ) {
			$list[] = $manager->bootstrap( $item->getSiteCode(), $item->getLanguageId(), $item->getCurrencyId() );
		}

		return map( $list );
	}


	protected function products( \Aimeos\MShop\ContextIface $context ) : \Aimeos\Map
	{
		$domains = ['media', 'price', 'product' => 'default', 'text'];
		$manager = \Aimeos\MShop::create( $context, 'product' );
		$items = $manager->search( $manager->filter()->add( 'product.type', '=~', 'demo-article' ), $domains );

		$manager = \Aimeos\MShop::create( $context, 'order/product' );
		$list = [];

		foreach( $items as $item ) {
			$list[$item->getId()] = $manager->create()->copyFrom( $item )->setQuantity( rand( 1, 3 ) );
		}

		return map( $list );
	}


	protected function services( \Aimeos\MShop\ContextIface $context ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $context, 'service' );
		$items = $manager->search( $manager->filter(), ['media', 'price', 'text'] );


		$manager = \Aimeos\MShop::create( $context, 'order/service' );
		$list = [];

		foreach( $items as $item ) {
			$list[$item->getId()] = $manager->create()->copyFrom( $item );
		}

		return map( $list )->groupBy( 'service.type' );
	}
}
