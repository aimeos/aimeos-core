<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'subscription.', $values );
	}


	/**
	 * Returns the ID of the base order
	 *
	 * @return string ID of the base order
	 */
	public function getOrderBaseId()
	{
		return $this->get( 'subscription.ordbaseid' );
	}


	/**
	 * Sets the ID of the base order item which the customer bought
	 *
	 * @param string $id ID of the base order
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderBaseId( $id )
	{
		return $this->set( 'subscription.ordbaseid', (string) $id );
	}


	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string ID of the ordered product
	 */
	public function getOrderProductId()
	{
		return $this->get( 'subscription.ordprodid' );
	}


	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $id ID of the ordered product
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderProductId( $id )
	{
		return $this->set( 'subscription.ordprodid', (string) $id );
	}


	/**
	 * Returns the date of the next subscription renewal
	 *
	 * @return string ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateNext()
	{
		return $this->get( 'subscription.datenext' );
	}


	/**
	 * Sets the date of the next subscription renewal
	 *
	 * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateNext( $date )
	{
		return $this->set( 'subscription.datenext', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the date when the subscription renewal ends
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateEnd()
	{
		return $this->get( 'subscription.dateend' );
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		return $this->set( 'subscription.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the time interval to pass between the subscription renewals
	 *
	 * @return string PHP time interval, e.g. "P1M2W"
	 */
	public function getInterval()
	{
		return $this->get( 'subscription.interval' );
	}


	/**
	 * Sets the time interval to pass between the subscription renewals
	 *
	 * @param string $value PHP time interval, e.g. "P1M2W"
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setInterval( $value )
	{
		if( preg_match( '/^P[0-9]+Y[0-9]+M[0-9]+W[0-9]+D$/', $value ) !== 1 ) {
			throw new \Aimeos\MShop\Subscription\Exception( sprintf( 'Invalid time interval format "%1$s"', $value ) );
		}

		return $this->set( 'subscription.interval', (string) $value );
	}


	/**
	 * Returns the current renewal period of the subscription product
	 *
	 * @return integer Current renewal period
	 */
	public function getPeriod()
	{
		return (int) $this->get( 'subscription.period', 1 );
	}


	/**
	 * Sets the current renewal period of the subscription product
	 *
	 * @param integer $value Current renewal period
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setPeriod( $value )
	{
		return $this->set( 'subscription.period', (int) $value );
	}


	/**
	 * Returns the product ID of the subscription product
	 *
	 * @return string Product ID
	 */
	public function getProductId()
	{
		return (string) $this->get( 'subscription.productid', '' );
	}


	/**
	 * Sets the product ID of the subscription product
	 *
	 * @param string $value Product ID
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setProductId( $value )
	{
		return $this->set( 'subscription.productid', (string) $value );
	}


	/**
	 * Returns the reason for the end of the subscriptions
	 *
	 * @return integer|null Reason code or NULL for no reason
	 */
	public function getReason()
	{
		return $this->get( 'subscription.reason' );
	}


	/**
	 * Sets the reason for the end of the subscriptions
	 *
	 * @param integer|null $value Reason code or NULL for no reason
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setReason( $value )
	{
		return $this->set( 'subscription.reason', ( is_numeric( $value ) ? (int) $value : null ) );
	}


	/**
	 * Returns the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 */
	public function getStatus()
	{
		return (int) $this->get( 'subscription.status', 1 );
	}


	/**
	 * Sets the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'subscription.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'subscription';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
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
				case 'subscription.period': $item = $item->setPeriod( $value ); break;
				case 'subscription.reason': $item = $item->setReason( $value ); break;
				case 'subscription.status': $item = $item->setStatus( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as associative list.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['subscription.ordbaseid'] = $this->getOrderBaseId();
		$list['subscription.ordprodid'] = $this->getOrderProductId();
		$list['subscription.productid'] = $this->getProductId();
		$list['subscription.datenext'] = $this->getDateNext();
		$list['subscription.dateend'] = $this->getDateEnd();
		$list['subscription.interval'] = $this->getInterval();
		$list['subscription.period'] = $this->getPeriod();
		$list['subscription.reason'] = $this->getReason();
		$list['subscription.status'] = $this->getStatus();

		return $list;
	}
}
