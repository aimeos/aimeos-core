<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Subscription
 */


namespace Aimeos\MShop\Subscription\Item;


/**
 * Default implementation of subscription item
 *
 * @package MShop
 * @subpackage Subscription
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Subscription\Item\Iface
{
	private $baseItem;


	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 * @param \Aimeos\MShop\Order\Item\Base\Iface|null $baseItem Order basket if available
	 */
	public function __construct( array $values = [], ?\Aimeos\MShop\Order\Item\Base\Iface $baseItem = null )
	{
		parent::__construct( 'subscription.', $values );
		$this->baseItem = $baseItem;
	}


	/**
	 * Returns the associated order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface|null Order base item
	 */
	public function getBaseItem() : ?\Aimeos\MShop\Order\Item\Base\Iface
	{
		return $this->baseItem;
	}


	/**
	 * Returns the ID of the base order
	 *
	 * @return string|null ID of the base order
	 */
	public function getOrderBaseId() : ?string
	{
		return $this->get( 'subscription.ordbaseid' );
	}


	/**
	 * Sets the ID of the base order item which the customer bought
	 *
	 * @param string $id ID of the base order
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderBaseId( string $id ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.ordbaseid', $id );
	}


	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string|null ID of the ordered product
	 */
	public function getOrderProductId() : ?string
	{
		return $this->get( 'subscription.ordprodid' );
	}


	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $id ID of the ordered product
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderProductId( string $id ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.ordprodid', $id );
	}


	/**
	 * Returns the date of the next subscription renewal
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateNext() : ?string
	{
		$value = $this->get( 'subscription.datenext' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the date of the next subscription renewal
	 *
	 * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateNext( string $date ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.datenext', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the date when the subscription renewal ends
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'subscription.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the time interval to pass between the subscription renewals
	 *
	 * @return string PHP time interval, e.g. "P1M2W"
	 */
	public function getInterval() : string
	{
		return $this->get( 'subscription.interval', '' );
	}


	/**
	 * Sets the time interval to pass between the subscription renewals
	 *
	 * @param string $value PHP time interval, e.g. "P1M2W"
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setInterval( string $value ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		if( strlen( $value ) > 1 && preg_match( '/^P([0-9]+Y)?([0-9]+M)?([0-9]+W)?([0-9]+D)?([0-9]+H)?$/', $value ) !== 1 ) {
			throw new \Aimeos\MShop\Subscription\Exception( sprintf( 'Invalid time interval format "%1$s"', $value ) );
		}

		return $this->set( 'subscription.interval', $value );
	}


	/**
	 * Returns the current renewal period of the subscription product
	 *
	 * @return int Current renewal period
	 */
	public function getPeriod() : int
	{
		return $this->get( 'subscription.period', 1 );
	}


	/**
	 * Sets the current renewal period of the subscription product
	 *
	 * @param int $value Current renewal period
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setPeriod( int $value ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.period', $value );
	}


	/**
	 * Returns the product ID of the subscription product
	 *
	 * @return string Product ID
	 */
	public function getProductId() : string
	{
		return $this->get( 'subscription.productid', '' );
	}


	/**
	 * Sets the product ID of the subscription product
	 *
	 * @param string $value Product ID
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setProductId( string $value ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.productid', $value );
	}


	/**
	 * Returns the reason for the end of the subscriptions
	 *
	 * @return int|null Reason code or NULL for no reason
	 */
	public function getReason() : ?int
	{
		return $this->get( 'subscription.reason' );
	}


	/**
	 * Sets the reason for the end of the subscriptions
	 *
	 * @param int|null $value Reason code or NULL for no reason
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setReason( ?int $value ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.reason', $value );
	}


	/**
	 * Returns the status of the subscriptions
	 *
	 * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
	 */
	public function getStatus() : int
	{
		return $this->get( 'subscription.status', 1 );
	}


	/**
	 * Sets the status of the subscriptions
	 *
	 * @return int Subscription status, i.e. "1" for enabled, "0" for disabled
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		return $this->set( 'subscription.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'subscription';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'subscription.ordbaseid': $item = $item->setOrderBaseId( $value ); break;
				case 'subscription.ordprodid': $item = $item->setOrderProductId( $value ); break;
				case 'subscription.productid': $item = $item->setProductId( $value ); break;
				case 'subscription.datenext': $item = $item->setDateNext( $value ); break;
				case 'subscription.dateend': $item = $item->setDateEnd( $value ); break;
				case 'subscription.interval': $item = $item->setInterval( $value ); break;
				case 'subscription.period': $item = $item->setPeriod( (int) $value ); break;
				case 'subscription.status': $item = $item->setStatus( (int) $value ); break;
				case 'subscription.reason': $item = $item->setReason( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['subscription.ordbaseid'] = $this->getOrderBaseId();
		$list['subscription.ordprodid'] = $this->getOrderProductId();
		$list['subscription.productid'] = $this->getProductId();
		$list['subscription.datenext'] = $this->getDateNext();
		$list['subscription.dateend'] = $this->getDateEnd();
		$list['subscription.interval'] = $this->getInterval();
		$list['subscription.period'] = $this->getPeriod();
		$list['subscription.status'] = $this->getStatus();
		$list['subscription.reason'] = $this->getReason();

		return $list;
	}
}
