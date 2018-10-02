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
	 * Tests if a coupon should be granted
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'coupon' );
		$codeManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'coupon/code' );

		$search = $manager->createSearch( true );
		$expr = [
			$search->compare( '==', 'coupon.code.code', $this->code ),
			$codeManager->createSearch( true )->getConditions(),
			$search->getConditions(),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		return (bool) count( $manager->searchItems( $search ) );
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
	 * Checks required fields and the types of the config array.
	 *
	 * @param array $config Config parameters
	 * @param array $attributes Attributes for the config array
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function checkConfig( array $config, array $attributes )
	{
		$errors = [];

		foreach( $config as $key => $def )
		{
			if( $def['required'] === true && ( !isset( $attributes[$key] ) || $attributes[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Configuration for "%1$s" is missing', $key );
				continue;
			}

			if( isset( $attributes[$key] ) )
			{
				switch( $def['type'] )
				{
					case 'boolean':
						if( !is_string( $attributes[$key] ) || $attributes[$key] !== '0' && $attributes[$key] !== '1' ) {
							$errors[$key] = sprintf( 'Not a true/false value' ); continue 2;
						}
						break;
					case 'string':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a string' ); continue 2;
						}
						break;
					case 'integer':
						if( ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not an integer number' ); continue 2;
						}
						break;
					case 'number':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = sprintf( 'Not a number' ); continue 2;
						}
						break;
					case 'date':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date' ); continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a date and time' ); continue 2;
						}
						break;
					case 'time':
						$pattern = '/^([0-2])?[0-9]:[0-5][0-9](:[0-5][0-9])?$/';
						if( !is_string( $attributes[$key] ) || preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = sprintf( 'Not a time' ); continue 2;
						}
						break;
					case 'select':
						if( !is_array( $def['default'] ) || !isset( $def['default'][$attributes[$key]] )
							&& !in_array( $attributes[$key], $def['default'] )
						) {
							$errors[$key] = sprintf( 'Not a listed value' ); continue 2;
						}
						break;
					case 'map':
						if( !is_array( $attributes[$key] ) ) {
							$errors[$key] = sprintf( 'Not a key/value map' ); continue 2;
						}
						break;
					default:
						throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Invalid type "%1$s"', $def['type'] ) );
				}
			}

			$errors[$key] = null;
		}

		return $errors;
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
		$config = $this->item->getConfig();

		if( isset( $config[$key] ) ) {
			return $config[$key];
		}

		return $default;
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
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$product = $productManager->findItem( $productCode, ['text', 'media', 'price'] );

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
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );
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
