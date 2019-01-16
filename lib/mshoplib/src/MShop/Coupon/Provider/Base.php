<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes )
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return [];
	}


	/**
	 * Tests if a valid coupon code should be granted
	 *
	 * The result depends on the configured restrictions and it doesn't test
	 * again if the coupon or the code itself are still available.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True of coupon can be granted, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		return true;
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object )
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Checks required fields and the types of the given data map
	 *
	 * @param array $criteria Multi-dimensional associative list of criteria configuration
	 * @param array $map Values to check agains the criteria
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $criteria, array $map )
	{
		$helper = new \Aimeos\MShop\Common\Item\Helper\Config\Standard( $this->getConfigItems( $criteria ) );
		return $helper->check( $map );
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
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList )
	{
		$list = [];

		foreach( $configList as $key => $config ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $config );
		}

		return $list;
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
		return $this->item->getConfigValue( $key, $default );
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
	 * Returns the stored coupon item.
	 *
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item
	 */
	protected function getItemBase()
	{
		return $this->item;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Outmost decorator object
	 */
	protected function getObject()
	{
		if( $this->object !== null ) {
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
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->findItem( $productCode, ['text', 'media', 'price'] );

		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		if( empty( $prices ) ) {
			$price = $priceManager->createItem();
		} else {
			$price = $priceManager->getLowestPrice( $prices, $quantity );
		}

		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
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
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basket object
	 * @param string $productCode Unique product code
	 * @param float $rebate Rebate amount that should be granted, will contain the remaining rebate if not fully used
	 * @param integer $quantity Number of products in basket
	 * @param string $stockType Unique code of the stock type the product is from
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] Order products with monetary rebates
	 */
	protected function createMonetaryRebateProducts( \Aimeos\MShop\Order\Item\Base\Iface $base,
		$productCode, &$rebate, $quantity = 1, $stockType = 'default' )
	{
		$orderProducts = [];
		$prices = $this->getPriceByTaxRate( $base );

		krsort( $prices );

		if( empty( $prices ) )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), 'price' );
			$prices = array( '0.00' => $manager->createItem() );
		}

		foreach( $prices as $taxrate => $price )
		{
			if( abs( $rebate ) < 0.01 ) {
				break;
			}

			if( ( $amount = $price->getValue() + $price->getCosts() ) < 0.01 ) {
				continue;
			}

			if( $amount < $rebate ) {
				$value = $amount; $rebate -= $amount;
			} else {
				$value = $rebate; $rebate = 0;
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
		$manager = \Aimeos\MShop::create( $this->getContext(), 'price' );

		foreach( $basket->getProducts() as $product )
		{
			$price = $product->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price, $product->getQuantity() );
		}

		foreach( $basket->getService( 'delivery' ) as $service )
		{
			$price = clone $service->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price );

		}

		foreach( $basket->getService( 'payment' ) as $service )
		{
			$price = clone $service->getPrice();
			$taxrate = $price->getTaxRate();

			if( !isset( $taxrates[$taxrate] ) ) {
				$taxrates[$taxrate] = $manager->createItem();
			}

			$taxrates[$taxrate]->addItem( $price );
		}

		return $taxrates;
	}
}
