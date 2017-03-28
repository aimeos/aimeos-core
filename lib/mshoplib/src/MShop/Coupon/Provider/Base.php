<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Abstract model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base implements Iface
{
	private $context;
	private $object;
	private $item;
	private $code = '';

	/**
	 * Initializes the coupon model.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $item, $code )
	{
		$this->context = $context;
		$this->item = $item;
		$this->code = $code;
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function updateCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		if( $this->getObject()->isAvailable( $base ) !== true )
		{
			$base->deleteCoupon( $this->code );
			return;
		}

		$this->deleteCoupon( $base );
		$this->addCoupon( $base );
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 */
	public function deleteCoupon( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$base->deleteCoupon( $this->code, true );
	}


	/**
	 * Tests if a coupon should be granted
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		return true;
	}


	/**
	 * Sets the reference of the outside object.
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outside provider or decorator
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object )
	{
		$this->object = $object;
	}


	/**
	 * Returns the stored context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the coupon code the provider is responsible for.
	 *
	 * @return string Coupon code
	 */
	protected function getCode()
	{
		return $this->code;
	}


	/**
	 * Returns the configuration value from the service item specified by its key.
	 *
	 * @param string $key Configuration key
	 * @param mixed $default Default value if configuration key isn't available
	 * @return mixed Value from service item configuration
	 */
	protected function getConfigValue( $key, $default = null )
	{
		$config = $this->item->getConfig();

		if( isset( $config[$key] ) ) {
			return $config[$key];
		}

		return $default;
	}


	/**
	 * Returns the stored coupon item.
	 *
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item
	 */
	protected function getItemBase()
	{
		return $this->item;
	}


	/**
	 * Returns the outmost decorator or a reference to the provider itself.
	 *
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Outmost object
	 */
	protected function getObject()
	{
		if( isset( $this->object ) ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Creates an order product from the product item.
	 *
	 * @param string $productCode Unique product code
	 * @param integer $quantity Number of products in basket
	 * @param string $stockType Unique code of the stock type the product is from
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Ordered product
	 */
	protected function createProduct( $productCode, $quantity = 1, $stockType = 'default' )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$search = $productManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'product.code', $productCode ) );
		$products = $productManager->searchItems( $search, array( 'text', 'media', 'price' ) );

		if( ( $product = reset( $products ) ) === false ) {
			throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'No product with code "%1$s" found', $productCode ) );
		}

		$priceManager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		if( empty( $prices ) ) {
			$price = $priceManager->createItem();
		} else {
			$price = $priceManager->getLowestPrice( $prices, $quantity );
		}

		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );
		$orderProduct = $orderBaseProductManager->createItem();

		$orderProduct->copyFrom( $product );
		$orderProduct->setQuantity( $quantity );
		$orderProduct->setStockType( $stockType );
		$orderProduct->setPrice( $price );
		$orderProduct->setFlags( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE );

		return $orderProduct;
	}


	/**
	 * Creates the order products for monetary rebates.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface Basket object
	 * @param string $productCode Unique product code
	 * @param float $rebate Rebate amount that should be granted
	 * @param integer $quantity Number of products in basket
	 * @param string $stockType Unique code of the stock type the product is from
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] Order products with monetary rebates
	 */
	protected function createMonetaryRebateProducts( \Aimeos\MShop\Order\Item\Base\Iface $base,
		$productCode, $rebate, $quantity = 1, $stockType = 'default' )
	{
		$orderProducts = [];
		$prices = $this->getPriceByTaxRate( $base );

		krsort( $prices );

		if( empty( $prices ) ) {
			$prices = array( '0.00' => \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' )->createItem() );
		}

		foreach( $prices as $taxrate => $price )
		{
			if( abs( $rebate ) < 0.01 ) {
				break;
			}

			$amount = $price->getValue() + $price->getCosts();

			if( $amount > 0 && $amount < $rebate )
			{
				$value = $price->getValue() + $price->getCosts();
				$rebate -= $value;
			}
			else
			{
				$value = $rebate;
				$rebate = '0.00';
			}

			$orderProduct = $this->createProduct( $productCode, $quantity, $stockType );

			$price = $orderProduct->getPrice();
			$price->setValue( -$value );
			$price->setRebate( $value );
			$price->setTaxRate( $taxrate );

			$orderProduct->setPrice( $price );

			$orderProducts[] = $orderProduct;
		}

		return $orderProducts;
	}


	/**
	 * Returns a list of tax rates and their price items for the given basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing the products, services, etc.
	 * @return array Associative list of tax rates as key and corresponding price items as value
	 */
	protected function getPriceByTaxRate( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$taxrates = [];
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );

		foreach( $basket->getProducts() as $product )
		{
			$price = $product->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price, $product->getQuantity() );
		}

		try
		{
			$price = $basket->getService( 'delivery' )->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price );
		}
		catch( \Exception $e ) { ; } // if delivery service isn't available

		try
		{
			$price = $basket->getService( 'payment' )->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price );
		}
		catch( \Exception $e ) { ; } // if payment service isn't available

		return $taxrates;
	}
}
