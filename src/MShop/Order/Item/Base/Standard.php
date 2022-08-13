<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base;


/**
 * Default implementation of the shopping basket.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard extends \Aimeos\MShop\Order\Item\Base\Base
{
	// protected is a workaround for serialize problem
	protected $price;
	protected $locale;
	protected $customer;
	protected $recalc = false;


	/**
	 * Initializes the shopping basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface[] $services List of order service items
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $coupons Associative list of coupon codes as keys and order product items as values
	 * @param \Aimeos\MShop\Customer\Item\Iface|null $custItem Customer item object
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [], array $services = [], array $coupons = [],
		?\Aimeos\MShop\Customer\Item\Iface $custItem = null )
	{
		parent::__construct( $price, $locale, $values, $products, $addresses, $services, $coupons );

		$this->price = $price;
		$this->locale = $locale;
		$this->customer = $custItem;
	}


	/**
	 * Clones internal objects of the order base item.
	 */
	public function __clone()
	{
		parent::__clone();

		$this->price = clone $this->price;
		$this->locale = clone $this->locale;
	}


	/**
	 * Specifies the data which should be serialized to JSON by json_encode().
	 *
	 * @return array<string,mixed> Data to serialize to JSON
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return parent::jsonSerialize() + [
			'price' => $this->price,
			'locale' => $this->locale,
			'customer' => $this->customer,
		];
	}


	/**
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param array $what Type of data
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 * @throws \Aimeos\MShop\Order\Exception if there are no products in the basket
	 */
	public function check( array $what = ['order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'] ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$this->notify( 'check.before', $what );

		if( in_array( 'order/base/product', $what ) && ( count( $this->getProducts() ) < 1 ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Basket empty' ) );
		}

		$this->notify( 'check.after', $what );

		return $this;
	}


	/**
	 * Returns the associated customer item
	 *
	 * @return \Aimeos\MShop\Customer\Item\Iface|null Customer item
	 */
	public function getCustomerItem() : ?\Aimeos\MShop\Customer\Item\Iface
	{
		return $this->customer;
	}


	/**
	 * Returns the code of the site the item is stored.
	 *
	 * @return string Site code (or empty string if not available)
	 */
	public function getSiteCode() : string
	{
		return $this->get( 'order.base.sitecode', '' );
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment() : string
	{
		return $this->get( 'order.base.comment', '' );
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setComment( ?string $comment ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		return $this->set( 'order.base.comment', (string) $comment );
	}


	/**
	 * Returns modify date/time of the order item base product.
	 *
	 * @return string|null Returns modify date/time of the order base item
	 */
	public function getTimeModified() : ?string
	{
		return $this->get( 'order.base.mtime' );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated() : ?string
	{
		return $this->get( 'order.base.ctime' );
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function editor() : string
	{
		return $this->get( 'order.base.editor', '' );
	}


	/**
	 * Returns the customer ID of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string
	{
		return $this->get( 'order.base.customerid', '' );
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setCustomerId( ?string $customerid ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		if( (string) $customerid !== $this->getCustomerId() )
		{
			$this->notify( 'setCustomerId.before', (string) $customerid );
			$this->set( 'order.base.customerid', (string) $customerid );
			$this->notify( 'setCustomerId.after', (string) $customerid );
		}

		return $this;
	}


	/**
	 * Returns the customer reference field of the order item
	 *
	 * @return string Customer reference for the order
	 */
	public function getCustomerReference() : string
	{
		return $this->get( 'order.base.customerref', '' );
	}


	/**
	 * Sets the customer reference field of the order item
	 *
	 * @param string $value Customer reference for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setCustomerReference( ?string $value ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		return $this->set( 'order.base.customerref', (string) $value );
	}


	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Object containing information
	 *  about site, language, country and currency
	 */
	public function locale() : \Aimeos\MShop\Locale\Item\Iface
	{
		return $this->locale;
	}


	/**
	 * Sets the locales for the basic order item.
	 *
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Object containing information
	 *  about site, language, country and currency
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$this->notify( 'setLocale.before', $locale );

		$this->locale = clone $locale;
		$this->setModified();

		$this->notify( 'setLocale.after', $locale );

		return $this;
	}


	/**
	 * Returns a price item with amounts calculated for the products, costs, etc.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate the customer has to pay
	 */
	public function getPrice() : \Aimeos\MShop\Price\Item\Iface
	{
		if( $this->recalc )
		{
			$price = $this->price->clear();

			foreach( $this->getServices() as $list )
			{
				foreach( $list as $service ) {
					$price = $price->addItem( $service->getPrice() );
				}
			}

			foreach( $this->getProducts() as $product ) {
				$price = $price->addItem( $product->getPrice(), $product->getQuantity() );
			}

			$this->price = $price;
			$this->recalc = false;
		}

		return $this->price;
	}


	/**
	 * Sets the modified flag of the object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface Order base item for method chaining
	 */
	public function setModified() : \Aimeos\MShop\Common\Item\Iface
	{
		$this->recalc = true;
		return parent::setModified();
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = $this;
		$locale = $item->locale();

		parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.id': $item = $item->setId( $value ); break;
				case 'order.base.customerid': $item = $item->setCustomerId( $value ); break;
				case 'order.base.languageid': $locale = $locale->setLanguageId( $value ); break;
				case 'order.base.customerref': $item = $item->setCustomerReference( $value ); break;
				case 'order.base.comment': $item = $item->setComment( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item->setLocale( $locale );
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$price = $this->getPrice();
		$locale = $this->locale();

		$list = parent::toArray( $private );

		$list['order.base.id'] = $this->getId();
		$list['order.base.sitecode'] = $this->getSiteCode();
		$list['order.base.customerid'] = $this->getCustomerId();
		$list['order.base.languageid'] = $locale->getLanguageId();
		$list['order.base.currencyid'] = $price->getCurrencyId();
		$list['order.base.price'] = $price->getValue();
		$list['order.base.costs'] = $price->getCosts();
		$list['order.base.rebate'] = $price->getRebate();
		$list['order.base.taxflag'] = $price->getTaxFlag();
		$list['order.base.taxvalue'] = $price->getTaxValue();
		$list['order.base.customerref'] = $this->getCustomerReference();
		$list['order.base.comment'] = $this->getComment();

		if( $private === true )
		{
			$list['order.base.siteid'] = $this->getSiteId();
			$list['order.base.mtime'] = $this->getTimeModified();
			$list['order.base.ctime'] = $this->getTimeCreated();
			$list['order.base.editor'] = $this->editor();
		}

		return $list;
	}


	/**
	 * Notifies listeners before the basket becomes an order.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function finish() : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$this->notify( 'setOrder.before' );
		return $this;
	}


	/**
	 * Checks if the price uses the same currency as the price in the basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item
	 */
	protected function checkPrice( \Aimeos\MShop\Price\Item\Iface $item )
	{
		$price = clone $this->price;
		$price->addItem( $item );
	}
}
