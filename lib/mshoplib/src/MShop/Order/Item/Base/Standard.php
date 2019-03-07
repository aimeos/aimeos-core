<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	protected $values;
	protected $recalc = false;
	protected $available = true;


	/**
	 * Initializes the shopping cart.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface[] $services List of order service items
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $coupons Associative list of coupon codes as keys and order product items as values
	 */
	public function __construct( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [],
		array $services = [], array $coupons = [] )
	{
		parent::__construct( $price, $locale, $values, $products, $addresses, $services, $coupons );

		$this->price = $price;
		$this->locale = $locale;
		$this->values = $values;
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
	 * Tests if all necessary items are available to create the order.
	 *
	 * @param integer $what Test for the specific type of completeness
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for method chaining
	 * @throws \Aimeos\MShop\Order\Exception if there are no products in the basket
	 */
	public function check( $what = self::PARTS_ALL )
	{
		$this->checkParts( $what );

		$this->notify( 'check.before', $what );

		if( ( $what & self::PARTS_PRODUCT ) && ( count( $this->getProducts() ) < 1 ) ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Basket empty' ) );
		}

		$this->notify( 'check.after', $what );

		return $this;
	}


	/**
	 * Returns the ID of the order if already available.
	 *
	 * @return string|null ID of the order item
	 */
	public function getId()
	{
		if( isset( $this->values['order.base.id'] ) ) {
			return (string) $this->values['order.base.id'];
		}

		return null;
	}


	/**
	 * Sets the id of the order base object.
	 *
	 * @param string $id Unique ID of the order base object
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setId( $id )
	{
		if( ( $this->values['order.base.id'] = \Aimeos\MShop\Common\Item\Base::checkId( $this->getId(), $id ) ) === null ) {
			$this->modified = true;
		} else {
			$this->modified = false;
		}

		return $this;
	}


	/**
	 * Returns the ID of the site the item is stored.
	 *
	 * @return string|null Site ID (or null if not available)
	 */
	public function getSiteId()
	{
		if( isset( $this->values['order.base.siteid'] ) ) {
			return (string) $this->values['order.base.siteid'];
		}
	}


	/**
	 * Returns the code of the site the item is stored.
	 *
	 * @return string Site code (or empty string if not available)
	 */
	public function getSiteCode()
	{
		if( isset( $this->values['order.base.sitecode'] ) ) {
			return (string) $this->values['order.base.sitecode'];
		}

		return '';
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment()
	{
		if( isset( $this->values['order.base.comment'] ) ) {
			return (string) $this->values['order.base.comment'];
		}

		return '';
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string $comment Comment for the order
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setComment( $comment )
	{
		if( (string) $comment !== $this->getComment() )
		{
			$this->values['order.base.comment'] = (string) $comment;
			$this->modified = true;
		}

		return $this;
	}


	/**
	 * Returns modify date/time of the order item base product.
	 *
	 * @return string|null Returns modify date/time of the order base item
	 */
	public function getTimeModified()
	{
		if( isset( $this->values['order.base.mtime'] ) ) {
			return (string) $this->values['order.base.mtime'];
		}
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		if( isset( $this->values['order.base.ctime'] ) ) {
			return (string) $this->values['order.base.ctime'];
		}
	}


	/**
	 * Returns the editor code of editor who created/modified the item at last.
	 *
	 * @return string Editorcode of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		if( isset( $this->values['order.base.editor'] ) ) {
			return (string) $this->values['order.base.editor'];
		}

		return '';
	}


	/**
	 * Returns the customer ID of the customer who has ordered.
	 *
	 * @return string Unique ID of the customer
	 */
	public function getCustomerId()
	{
		if( isset( $this->values['order.base.customerid'] ) ) {
			return (string) $this->values['order.base.customerid'];
		}

		return '';
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string $customerid Unique ID of the customer
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function setCustomerId( $customerid )
	{
		if( (string) $customerid !== $this->getCustomerId() )
		{
			$this->notify( 'setCustomerId.before', $customerid );

			$this->values['order.base.customerid'] = (string) $customerid;
			$this->modified = true;

			$this->notify( 'setCustomerId.after', $customerid );
		}

		return $this;
	}


	/**
	 * Returns the locales for the basic order item.
	 *
	 * @return \Aimeos\MShop\Locale\Item\Iface Object containing information
	 *  about site, language, country and currency
	 */
	public function getLocale()
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
	public function setLocale( \Aimeos\MShop\Locale\Item\Iface $locale )
	{
		$this->notify( 'setLocale.before', $locale );

		$this->locale = clone $locale;
		$this->modified = true;

		$this->notify( 'setLocale.after', $locale );

		return $this;
	}


	/**
	 * Returns a price item with amounts calculated for the products, costs, etc.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item with price, costs and rebate the customer has to pay
	 */
	public function getPrice()
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
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return $this->available;
	}


	/**
	 * Sets the general availability of the item
	 *
	 * @return boolean $value True if available, false if not
	 */
	public function setAvailable( $value )
	{
		$this->available = (bool) $value;
	}


	/**
	 * Sets the modified flag of the object.
	 */
	public function setModified()
	{
		parent::setModified();
		$this->recalc = true;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = $this;
		$locale = $item->getLocale();

		unset( $list['order.base.siteid'] );
		unset( $list['order.base.ctime'] );
		unset( $list['order.base.mtime'] );
		unset( $list['order.base.editor'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.id': !$private ?: $item = $item->setId( $value ); break;
				case 'order.base.customerid': !$private ?: $item = $item->setCustomerId( $value ); break;
				case 'order.base.languageid': !$private ?: $locale = $locale->setLanguageId( $value ); break;
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
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$price = $this->getPrice();
		$locale = $this->getLocale();

		$list = array(
			'order.base.customerid' => $this->getCustomerId(),
			'order.base.sitecode' => $this->getSiteCode(),
			'order.base.languageid' => $locale->getLanguageId(),
			'order.base.currencyid' => $price->getCurrencyId(),
			'order.base.price' => $price->getValue(),
			'order.base.costs' => $price->getCosts(),
			'order.base.rebate' => $price->getRebate(),
			'order.base.taxvalue' => $price->getTaxValue(),
			'order.base.taxflag' => $price->getTaxFlag(),
			'order.base.comment' => $this->getComment(),
		);

		if( $private === true )
		{
			$list['order.base.id'] = $this->getId();
			$list['order.base.siteid'] = $this->getSiteId();
			$list['order.base.mtime'] = $this->getTimeModified();
			$list['order.base.ctime'] = $this->getTimeCreated();
			$list['order.base.editor'] = $this->getEditor();
		}

		return $list;
	}


	/**
	 * Notifies listeners before the basket becomes an order.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item for chaining method calls
	 */
	public function finish()
	{
		$this->notify( 'setOrder.before' );
		return $this;
	}


	/**
	 * Checks the constants for the different parts of the basket.
	 *
	 * @param integer $value Part constant
	 * @throws \Aimeos\MShop\Order\Exception If parts constant is invalid
	 */
	protected function checkParts( $value )
	{
		$value = (int) $value;

		if( $value < self::PARTS_NONE || $value > self::PARTS_ALL ) {
			throw new \Aimeos\MShop\Order\Exception( sprintf( 'Flags not within allowed range' ) );
		}
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
