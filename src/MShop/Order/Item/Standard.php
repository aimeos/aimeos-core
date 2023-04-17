<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Default implementation of an order invoice item.
 *
 * @property int oldPaymentStatus Last delivery status before it was changed by setDeliveryStatus()
 * @property int oldDeliveryStatus Last payment status before it was changed by setPaymentStatus()
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base
	implements \Aimeos\MShop\Order\Item\Iface
{
	// protected is a workaround for serialize problem
	protected ?\Aimeos\MShop\Customer\Item\Iface $customer;
	protected \Aimeos\MShop\Locale\Item\Iface $locale;
	protected \Aimeos\MShop\Price\Item\Iface $price;
	protected bool $recalc = false;


	/**
	 * Initializes the shopping basket.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Service\Iface[] $services List of order service items
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $coupons Associative list of coupon codes as keys and order product items as values
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
	 * Returns the order number
	 *
	 * @return string Order number
	 */
	public function getOrderNumber() : string
	{
		if( $fcn = self::macro( 'ordernumber' ) ) {
			return (string) $fcn( $this );
		}

		return (string) $this->getId();
	}


	/**
	 * Returns the number of the invoice.
	 *
	 * @return string Invoice number
	 */
	public function getInvoiceNumber() : string
	{
		if( $fcn = self::macro( 'invoicenumber' ) ) {
			return (string) $fcn( $this );
		}

		return (string) $this->get( 'order.invoiceno', '' );
	}


	/**
	 * Sets the number of the invoice.
	 *
	 * @param string $value Invoice number
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setInvoiceNumber( string $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.invoiceno', $value );
	}


	/**
	 * Returns the channel of the invoice (repeating, web, phone, etc).
	 *
	 * @return string Invoice channel
	 */
	public function getChannel() : string
	{
		return (string) $this->get( 'order.channel', '' );
	}


	/**
	 * Sets the channel of the invoice.
	 *
	 * @param string $channel Invoice channel
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setChannel( string $channel ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.channel', $this->checkCode( $channel ) );
	}


	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery() : ?string
	{
		$value = $this->get( 'order.datedelivery' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDateDelivery( ?string $date ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.datedelivery', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the purchase date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment() : ?string
	{
		$value = $this->get( 'order.datepayment' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the purchase date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDatePayment( ?string $date ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.datepayment', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return int Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusDelivery() : int
	{
		return $this->get( 'order.statusdelivery', -1 );
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param int $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusDelivery( int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->set( '.statusdelivery', $this->get( 'order.statusdelivery' ) );
		return $this->set( 'order.statusdelivery', $status );
	}


	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return int Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusPayment() : int
	{
		return $this->get( 'order.statuspayment', -1 );
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param int $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusPayment( int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $status !== $this->getStatusPayment() ) {
			$this->set( 'order.datepayment', date( 'Y-m-d H:i:s' ) );
		}

		$this->set( '.statuspayment', $this->get( 'order.statuspayment' ) );
		return $this->set( 'order.statuspayment', $status );
	}


	/**
	 * Returns the related invoice ID.
	 *
	 * @return string Related invoice ID
	 */
	public function getRelatedId() : string
	{
		return (string) $this->get( 'order.relatedid', '' );
	}


	/**
	 * Sets the related invoice ID.
	 *
	 * @param string|null $id Related invoice ID
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If ID is invalid
	 */
	public function setRelatedId( ?string $id ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.relatedid', (string) $id );
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
		return $this->get( 'order.sitecode', '' );
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment() : string
	{
		return $this->get( 'order.comment', '' );
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
	 */
	public function setComment( ?string $comment ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.comment', (string) $comment );
	}


	/**
	 * Returns the customer ID of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId() : string
	{
		return $this->get( 'order.customerid', '' );
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
	 */
	public function setCustomerId( ?string $customerid ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( (string) $customerid !== $this->getCustomerId() )
		{
			$this->notify( 'setCustomerId.before', (string) $customerid );
			$this->set( 'order.customerid', (string) $customerid );
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
		return $this->get( 'order.customerref', '' );
	}


	/**
	 * Sets the customer reference field of the order item
	 *
	 * @param string $value Customer reference for the order
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
	 */
	public function setCustomerReference( ?string $value ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.customerref', (string) $value );
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
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item for chaining method calls
	 */
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale ) : \Aimeos\MShop\Order\Item\Iface
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
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );
		$locale = $item->locale();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.channel': $item = $item->setChannel( $value ); break;
				case 'order.invoiceno': !$private ?: $item = $item->setInvoiceNumber( $value ); break;
				case 'order.statusdelivery': $item = $item->setStatusDelivery( (int) $value ); break;
				case 'order.statuspayment': $item = $item->setStatusPayment( (int) $value ); break;
				case 'order.datedelivery': $item = $item->setDateDelivery( $value ); break;
				case 'order.datepayment': $item = $item->setDatePayment( $value ); break;
				case 'order.relatedid': $item = $item->setRelatedId( $value ); break;
				case 'order.customerid': $item = $item->setCustomerId( $value ); break;
				case 'order.languageid': $locale = $locale->setLanguageId( $value ); break;
				case 'order.customerref': $item = $item->setCustomerReference( $value ); break;
				case 'order.comment': $item = $item->setComment( $value ); break;
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

		$list['order.channel'] = $this->getChannel();
		$list['order.invoiceno'] = $this->getInvoiceNumber();
		$list['order.statusdelivery'] = $this->getStatusDelivery();
		$list['order.statuspayment'] = $this->getStatusPayment();
		$list['order.datedelivery'] = $this->getDateDelivery();
		$list['order.datepayment'] = $this->getDatePayment();
		$list['order.relatedid'] = $this->getRelatedId();
		$list['order.sitecode'] = $this->getSiteCode();
		$list['order.customerid'] = $this->getCustomerId();
		$list['order.languageid'] = $locale->getLanguageId();
		$list['order.currencyid'] = $price->getCurrencyId();
		$list['order.price'] = $price->getValue();
		$list['order.costs'] = $price->getCosts();
		$list['order.rebate'] = $price->getRebate();
		$list['order.taxflag'] = $price->getTaxFlag();
		$list['order.taxvalue'] = $price->getTaxValue();
		$list['order.customerref'] = $this->getCustomerReference();
		$list['order.comment'] = $this->getComment();

		return $list;
	}
}
