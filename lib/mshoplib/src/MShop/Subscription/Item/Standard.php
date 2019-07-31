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
	private $values;


	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'subscription.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the ID of the base order
	 *
	 * @return string ID of the base order
	 */
	public function getOrderBaseId()
	{
		if( isset( $this->values['subscription.ordbaseid'] ) ) {
			return (string) $this->values['subscription.ordbaseid'];
		}
	}


	/**
	 * Sets the ID of the base order item which the customer bought
	 *
	 * @param string $id ID of the base order
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderBaseId( $id )
	{
		if( (string) $id !== $this->getOrderBaseId() )
		{
			$this->values['subscription.ordbaseid'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the ID of the ordered product
	 *
	 * @return string ID of the ordered product
	 */
	public function getOrderProductId()
	{
		if( isset( $this->values['subscription.ordprodid'] ) ) {
			return (string) $this->values['subscription.ordprodid'];
		}
	}


	/**
	 * Sets the ID of the ordered product item which the customer subscribed for
	 *
	 * @param string $id ID of the ordered product
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setOrderProductId( $id )
	{
		if( (string) $id !== $this->getOrderProductId() )
		{
			$this->values['subscription.ordprodid'] = (string) $id;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the date of the next subscription renewal
	 *
	 * @return string ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateNext()
	{
		if( isset( $this->values['subscription.datenext'] ) ) {
			return (string) $this->values['subscription.datenext'];
		}
	}


	/**
	 * Sets the date of the next subscription renewal
	 *
	 * @param string $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateNext( $date )
	{
		if( (string) $date !== $this->getDateNext() )
		{
			$this->values['subscription.datenext'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the date when the subscription renewal ends
	 *
	 * @return string|null ISO date in "YYYY-MM-DD HH:mm:ss" format
	 */
	public function getDateEnd()
	{
		if( isset( $this->values['subscription.dateend'] ) ) {
			return (string) $this->values['subscription.dateend'];
		}
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in "YYYY-MM-DD HH:mm:ss" format
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( (string) $date !== $this->getDateEnd() )
		{
			$this->values['subscription.dateend'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the time interval to pass between the subscription renewals
	 *
	 * @return string PHP time interval, e.g. "P1M2W"
	 */
	public function getInterval()
	{
		if( isset( $this->values['subscription.interval'] ) ) {
			return (string) $this->values['subscription.interval'];
		}
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

		if( (string) $value !== $this->getInterval() )
		{
			$this->values['subscription.interval'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the current renewal period of the subscription product
	 *
	 * @return integer Current renewal period
	 */
	public function getPeriod()
	{
		if( isset( $this->values['subscription.period'] ) ) {
			return (int) $this->values['subscription.period'];
		}

		return 1;
	}


	/**
	 * Sets the current renewal period of the subscription product
	 *
	 * @param integer $value Current renewal period
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setPeriod( $value )
	{
		if( (string) $value !== $this->getPeriod() )
		{
			$this->values['subscription.period'] = (int) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the product code of the subscription product
	 *
	 * @return string Product code
	 */
	public function getProductCode()
	{
		if( isset( $this->values['subscription.prodcode'] ) ) {
			return (string) $this->values['subscription.prodcode'];
		}

		return '';
	}


	/**
	 * Sets the product code of the subscription product
	 *
	 * @param string $value Product code
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setProductCode( $value )
	{
		if( (string) $value !== $this->getProductCode() )
		{
			$this->values['subscription.prodcode'] = $this->checkCode( $value );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the reason for the end of the subscriptions
	 *
	 * @return integer|null Reason code or NULL for no reason
	 */
	public function getReason()
	{
		if( isset( $this->values['subscription.reason'] ) ) {
			return (int) $this->values['subscription.reason'];
		}
	}

	/**
	 * Sets the reason for the end of the subscriptions
	 *
	 * @param integer|null $value Reason code or NULL for no reason
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setReason( $value )
	{
		if( $value !== $this->getReason() )
		{
			$this->values['subscription.reason'] = ( is_numeric( $value ) ? (int) $value : null );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 */
	public function getStatus()
	{
		if( isset( $this->values['subscription.status'] ) ) {
			return (int) $this->values['subscription.status'];
		}

		return 1;
	}


	/**
	 * Sets the status of the subscriptions
	 *
	 * @return integer Subscription status, i.e. "1" for enabled, "0" for disabled
	 * @return \Aimeos\MShop\Subscription\Item\Iface Subscription item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (string) $status !== $this->getStatus() )
		{
			$this->values['subscription.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
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
				case 'subscription.prodcode': $item = $item->setProductCode( $value ); break;
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
		$list['subscription.prodcode'] = $this->getProductCode();
		$list['subscription.datenext'] = $this->getDateNext();
		$list['subscription.dateend'] = $this->getDateEnd();
		$list['subscription.interval'] = $this->getInterval();
		$list['subscription.period'] = $this->getPeriod();
		$list['subscription.reason'] = $this->getReason();
		$list['subscription.status'] = $this->getStatus();

		return $list;
	}
}
