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
		return ['Order', 'DemoAddCustomerData', 'DemoAddCouponData', 'DemoAddProductData', 'DemoAddServiceData'];
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
		$filter = $manager->filter()->add( 'order.channel', '==', 'demo' );
		$manager->delete( $manager->search( $filter ) );


		if( $value === '1' ) {
			$this->addDemoData();
		}
	}


	/**
	 * Adds the demo orders.
	 */
	protected function addDemoData()
	{
		$num = 0;
		$paystatus = [
			\Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
			\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED,
			\Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
			\Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED,
			\Aimeos\MShop\Order\Item\Base::PAY_PENDING
		];

		$context = $this->context();
		$site = $context->config()->get( 'setup/site', 'default' );
		$customer = \Aimeos\MShop::create( $context, 'customer' )->find( $site . 'demo@example.com' );

		foreach( $this->locales() as $locale )
		{
			$lcontext = clone $context;
			$lcontext->setLocale( $locale );

			$products = $this->products( $lcontext );
			$services = $this->services( $lcontext );

			$manager = \Aimeos\MShop::create( $lcontext, 'order' );
			$manager->begin();

			for( $i = 0; $i < 10; $i++ )
			{
				for( $j = 0; $j < floor( $i / 4 ) + 1; $j++ )
				{
					$lcontext->setDateTime( date( 'Y-m-d H:i:s', time() + ( -9 + $i ) * 86400 - 15000 + ( $num % 10 ) * 1357 ) );

					$item = $manager->create()
						->setChannel( 'demo' )
						->setCustomerId( $customer->getId() )
						->setStatusPayment( $paystatus[$num % 5] )
						->setDatePayment( $lcontext->datetime() )
						->setLocale( $locale );

					foreach( map( $services->get( 'delivery', [] ) )->random( 1 ) as $service ) {
						$item->addService( clone $service, 'delivery' );
					}

					foreach( map( $services->get( 'payment', [] ) )->random( 1 ) as $service ) {
						$item->addService( clone $service, 'payment' );
					}

					foreach( $products->random( rand( 1, 2 ) ) as $product ) {
						$item->addProduct( clone $product );
					}

					$item->addAddress( $this->address( $customer->getPaymentAddress(), $num ), 'payment' );

					$manager->save( $item );
					++$num;
				}
			}

			$manager->commit();
			$num += 7;
		}
	}


	/**
	 * Returns the order address item based on the passed number
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Standard $address Address item
	 * @param int $num Sequential number
	 * @return \Aimeos\MShop\Order\Item\Address\Iface Order address item
	 */
	protected function address( \Aimeos\MShop\Common\Item\Address\Standard $address, int $num ) : \Aimeos\MShop\Order\Item\Address\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'order/address' );
		$firstname = ['John', 'Jose', 'Sandrine', 'Anna', 'Angel', 'Emma', 'Peter', 'Lucy', 'Paul', 'Lilly'];
		$lastname = ['Doe', 'Alva', 'Hugo', 'Lodz', 'Hidalgo', 'Wilson', 'Evans', 'Singh', 'Li', 'Müller'];
		$country = ['AU', 'BR', 'FR', 'PL', 'ES', 'US', 'ZA', 'IN', 'CN', 'DE'];

		return $manager->create()->copyFrom( $address )
			->setFirstname( $firstname[$num % 10] )
			->setLastname( $lastname[$num % 10] )
			->setCountryId( $country[$num % 10] )
			->setCompany( '' );
	}


	/**
	 * Returns the available locale objects
	 *
	 * @return \Aimeos\Map List of locale objects
	 */
	protected function locales() : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'locale' );
		$items = $manager->search( $manager->filter()->add( 'locale.site.code', '==', 'default' ) );
		$list = [];

		foreach( $items as $item ) {
			$list[] = $manager->bootstrap( $item->getSiteCode(), $item->getLanguageId(), $item->getCurrencyId() );
		}

		return map( $list );
	}


	/**
	 * Returns the available products
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\Map List of product items
	 */
	protected function products( \Aimeos\MShop\ContextIface $context ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $context, 'product' );
		$items = $manager->search( $manager->filter()->add( 'product.code', '=~', 'demo-article' ), ['media', 'price', 'text'] );

		$manager = \Aimeos\MShop::create( $context, 'order/product' );
		$list = [];

		foreach( $items as $item )
		{
			$price = $item->getRefItems( 'price', 'default', 'default' )->first();
			$list[$item->getId()] = $manager->create()->copyFrom( $item )->setQuantity( rand( 1, 3 ) )->setPrice( $price );
		}

		return map( $list );
	}


	/**
	 * Returns the available services
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\Map List of service items
	 */
	protected function services( \Aimeos\MShop\ContextIface $context ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $context, 'service' );
		$items = $manager->search( $manager->filter(), ['media', 'price', 'text'] );

		$manager = \Aimeos\MShop::create( $context, 'order/service' );
		$list = [];

		foreach( $items as $item )
		{
			$price = $item->getRefItems( 'price', 'default', 'default' )->first();
			$list[$item->getId()] = $manager->create()->copyFrom( $item )->setPrice( $price );
		}

		return map( $list )->groupBy( 'order.service.type' );
	}
}
