<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
abstract class Base
	implements Iface, \Aimeos\MW\Macro\Iface
{
	use \Aimeos\MW\Macro\Traits;

	private $context;
	private $object;
	private $item;
	private $code;

	/**
	 * Initializes the coupon model.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Coupon\Item\Iface $item, string $code )
	{
		$this->context = $context;
		$this->item = $item;
		$this->code = $code;
	}


	/**
	 * Returns the price the discount should be applied to
	 *
	 * The result depends on the configured restrictions and it must be less or
	 * equal to the passed price.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return \Aimeos\MShop\Price\Item\Iface New price that should be used
	 */
	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $base ) : \Aimeos\MShop\Price\Item\Iface
	{
		$price = \Aimeos\MShop::create( $this->context, 'price' )->create();

		foreach( $base->getProducts() as $product ) {
			$price = $price->addItem( $product->getPrice(), $product->getQuantity() );
		}

		return $price;
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		return [];
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
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
	 * @return bool True of coupon can be granted, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base ) : bool
	{
		return true;
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $object Reference to the outmost provider or decorator
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Coupon\Provider\Iface $object ) : \Aimeos\MShop\Coupon\Provider\Iface
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
	protected function checkConfig( array $criteria, array $map ) : array
	{
		$helper = new \Aimeos\MShop\Common\Helper\Config\Standard( $this->getConfigItems( $criteria ) );
		return $helper->check( $map );
	}


	/**
	 * Returns the coupon code the provider is responsible for.
	 *
	 * @return string Coupon code
	 */
	protected function getCode() : string
	{
		return $this->code;
	}


	/**
	 * Returns the criteria attribute items for the backend configuration
	 *
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of criteria attribute items
	 */
	protected function getConfigItems( array $configList ) : array
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
	protected function getConfigValue( string $key, $default = null )
	{
		return $this->item->getConfigValue( $key, $default );
	}


	/**
	 * Returns the stored context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the stored coupon item.
	 *
	 * @return \Aimeos\MShop\Coupon\Item\Iface Coupon item
	 */
	protected function getItem() : \Aimeos\MShop\Coupon\Item\Iface
	{
		return $this->item;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Outmost decorator object
	 */
	protected function getObject() : \Aimeos\MShop\Coupon\Provider\Iface
	{
		if( $this->object !== null ) {
			return $this->object;
		}

		return $this;
	}


	/**
	 * Creates an order product for the given product code
	 *
	 * @param string $prodcode Unique product code
	 * @param float $quantity Number of products
	 * @param string $stocktype Unique stock type code for the order product
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product
	 */
	protected function createProduct( string $prodcode, float $quantity = 1,
		string $stocktype = 'default' ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( $prodcode, ['text', 'media', 'price'] );

		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$prices = $product->getRefItems( 'price', 'default', 'default' );

		if( !$prices->isEmpty() ) {
			$price = $priceManager->getLowestPrice( $prices, $quantity );
		} else {
			$price = $priceManager->create();
		}

		return \Aimeos\MShop::create( $this->context, 'order/base/product' )->create()
			->copyFrom( $product )->setQuantity( $quantity )->setStockType( $stocktype )->setPrice( $price )
			->setFlags( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE );
	}


	/**
	 * Creates the order products for monetary rebates.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basket object
	 * @param string $prodcode Unique product code
	 * @param float $rebate Rebate amount that should be granted, will contain the remaining rebate if not fully used
	 * @param float $quantity Number of products in basket
	 * @param string $stockType Unique code of the stock type the product is from
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] Order products with monetary rebates
	 */
	protected function createRebateProducts( \Aimeos\MShop\Order\Item\Base\Iface $base,
		string $prodcode, float &$rebate, float $quantity = 1, string $stockType = 'default' ) : array
	{
		$orderProducts = [];

		if( ( $prices = $this->getPriceByTaxRate( $base ) )->isEmpty() ) {
			$prices = ['0.00' => \Aimeos\MShop::create( $this->getContext(), 'price' )->create()];
		}

		foreach( $prices as $taxrate => $price )
		{
			if( $rebate < 0.01 ) {
				break;
			}

			if( ( $amount = $price->getValue() + $price->getCosts() ) < 0.01 ) {
				continue;
			}

			if( $amount <= $rebate ) {
				$value = $price->getValue(); $costs = $price->getCosts(); $rebate -= $amount;
			} elseif( ( $amount = $price->getValue() ) <= $rebate ) {
				$value = $amount; $costs = $rebate - $amount; $rebate = 0;
			} else {
				$value = $rebate; $costs = 0; $rebate = 0;
			}

			$orderProduct = $this->createProduct( $prodcode, $quantity, $stockType );
			$price = $orderProduct->getPrice()->setTaxRate( $taxrate )
				->setValue( -$value )->setCosts( -$costs )->setRebate( $value + $costs );

			$orderProducts[] = $orderProduct->setPrice( $price );
		}

		return $orderProducts;
	}


	/**
	 * Returns a list of tax rates and their price items for the given basket.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing the products, services, etc.
	 * @return \Aimeos\Map Associative list of tax rates as key and price items implementing \Aimeos\MShop\Price\Item\Iface
	 */
	protected function getPriceByTaxRate( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\Map
	{
		$prices = map();
		$manager = \Aimeos\MShop::create( $this->getContext(), 'price' );

		$map = $basket->getCoupons();
		$products = $map[$this->getCode()] ?? [];

		foreach( $basket->getProducts() as $key => $item )
		{
			if( !in_array( $item, $products, true ) )
			{
				$price = $item->getPrice();
				$rate = $price->getTaxRate();
				$prices[$rate] = $prices->get( $rate, $manager->create() )->addItem( $price, $item->getQuantity() );
			}
		}

		foreach( $basket->getServices() as $services )
		{
			foreach( $services as $item )
			{
				$price = $item->getPrice();
				$rate = $price->getTaxRate();
				$prices[$rate] = $prices->get( $rate, $manager->create() )->addItem( $price );
			}
		}

		return $prices->krsort();
	}
}
