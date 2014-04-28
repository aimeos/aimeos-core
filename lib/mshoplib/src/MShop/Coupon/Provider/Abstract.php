<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Abstract model for coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class MShop_Coupon_Provider_Abstract implements MShop_Coupon_Provider_Factory_Interface
{
	private $_context = null;
	private $_couponItem = null;
	private $_code = '';
	private $_outer = null;

	/**
	 * Initializes the coupon model.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param MShop_Coupon_Item_Interface $item Coupon item to set
	 * @param string $code Coupon code entered by the customer
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Coupon_Item_Interface $item, $code, &$outer )
	{
		$this->_context = $context;
		$this->_couponItem = $item;
		$this->_code = $code;
		$this->_outer = &$outer;
	}


	/**
	 * Updates the result of a coupon to the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function updateCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$config = $this->_getItem()->getConfig();

		$this->deleteCoupon( $base );

		if( $this->_checkConstraints( $base, $config ) !== false ) {
			$this->addCoupon( $base );
		}
	}


	/**
	 * Removes the result of a coupon from the order base instance.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 */
	public function deleteCoupon( MShop_Order_Item_Base_Interface $base )
	{
		$base->deleteCoupon( $this->_code );
	}

	/**
	 *
	 * @param MShop_Order_Item_Base_Interface $base
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $base )
	{
		return true;
	}

	/**
	 * Returns the stored context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the coupon code the provider is responsible for.
	 *
	 * @return string Coupon code
	 */
	protected function _getCode()
	{
		return $this->_code;
	}


	/**
	 * Returns the stored coupon item.
	 *
	 * @return MShop_Coupon_Item_Interface Coupon item
	 */
	protected function _getItem()
	{
		return $this->_couponItem;
	}


	/**
	 * Returns the last decorator
	 *
	 * @return MShop_Coupon_Provider_Decorator_Interface outer object
	 */
	protected function _getOuterObject()
	{
		return $this->_outer;
	}


	/**
	 * Checks if the current basket matches the contraints.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Basic order of the customer
	 * @param array $config Associative array with config content
	 * @return boolean True if the basket matches the constraints, false if not
	 */
	protected function _checkConstraints( MShop_Order_Item_Base_Interface $base, array $config )
	{
		if( isset( $config['minorder'] ) && $config['minorder'] > $base->getPrice()->getValue() ) {
			return false;
		}

		if( isset( $config['reqproduct'] ) )
		{
			foreach( $base->getProducts() AS $product )
			{
				if( $product->getProductId() == $config['reqproduct'] ) {
					return true;
				}
			}

			return false;
		}

		return true;
	}


	/**
	 * Creates an order product from the product item.
	 *
	 * @param string $productCode Unique product code
	 * @param integer $quantity Number of products in basket
	 * @return MShop_Order_Base_Product_Interface Ordered product
	 */
	protected function _createProduct( $productCode, $quantity = 1 )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $productManager->createSearch(true);
		$search->setConditions( $search->compare( '==', 'product.code', $productCode ) );
		$products = $productManager->searchItems( $search, array( 'text', 'media', 'price' ) );

		if( ( $product = reset( $products ) ) === false ) {
			throw new MShop_Coupon_Exception( sprintf( 'No product for code "%1$s" found', $productCode ) );
		}

		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$orderBaseManager = $orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$orderProduct = $orderBaseProductManager->createItem();

		$orderProduct->copyFrom( $product );
		$orderProduct->setQuantity( $quantity );
		$orderProduct->setPrice( $this->_getProductPrice( $product, $quantity ) );
		$orderProduct->setFlags( MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE );

		return $orderProduct;
	}


	/**
	 * Returns the price item of the product that is valid for the given quantity.
	 *
	 * @param MShop_Product_Item_Interface $product Product item
	 * @param integer $quantity Amount of products ordered
	 * @return MShop_Price_Item_Interface Price item for the product/quantity combination
	 */
	protected function _getProductPrice( MShop_Product_Item_Interface $product, $quantity = 1 )
	{
		$prices = $product->getRefItems( 'price' );

		if( ( $priceItem = reset( $prices ) ) !== false )
		{
			foreach( $prices as $price )
			{
				$amount = $price->getQuantity();

				if( $amount <= $quantity && $amount > $priceItem->getQuantity() ) {
					$priceItem = $price;
				}
			}

			if( $priceItem->getQuantity() <= $quantity ) {
				return $priceItem;
			}
		}

		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_context );
		return $priceManager->createItem();
	}
}
