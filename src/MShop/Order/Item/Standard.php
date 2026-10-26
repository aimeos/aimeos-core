<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Default implementation of an order invoice item.
 *
 * @property int $oldPaymentStatus Last delivery status before it was changed by setDeliveryStatus()
 * @property int $oldDeliveryStatus Last payment status before it was changed by setPaymentStatus()
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base
	implements \Aimeos\MShop\Order\Item\Iface
{
	/**
	 * Returns the order number
	 *
	 * @return string Order number
	 */
	public function getOrderNumber() : string
	{
		if( self::macro( 'ordernumber' ) ) {
			return (string) $this->call( 'ordernumber' );
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
		if( self::macro( 'invoicenumber' ) ) {
			return (string) $this->call( 'invoicenumber' );
		}

		return (string) $this->get( 'order.invoiceno', '' );
	}


	/**
	 * Sets the number of the invoice.
	 *
	 * @param string|null $value Invoice number
	 * @return static Order item for chaining method calls
	 */
	public function setInvoiceNumber( ?string $value ) : static
	{
		return $this->set( 'order.invoiceno', (string) $value );
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
	 * @param string|null $channel Invoice channel
	 * @return static Order item for chaining method calls
	 */
	public function setChannel( ?string $channel ) : static
	{
		return $this->set( 'order.channel', \Aimeos\Utils::code( (string) $channel ) );
	}


	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery() : ?string
	{
		$value = $this->get( 'order.datedelivery' );
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return static Order item for chaining method calls
	 */
	public function setDateDelivery( ?string $date ) : static
	{
		return $this->set( 'order.datedelivery', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the purchase date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment() : ?string
	{
		$value = $this->get( 'order.datepayment' );
		return $value ? substr( (string) $value, 0, 19 ) : null;
	}


	/**
	 * Sets the purchase date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return static Order item for chaining method calls
	 */
	public function setDatePayment( ?string $date ) : static
	{
		return $this->set( 'order.datepayment', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return int Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusDelivery() : int
	{
		return (int) $this->get( 'order.statusdelivery', -1 );
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param int $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return static Order item for chaining method calls
	 */
	public function setStatusDelivery( int $status ) : static
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
		return (int) $this->get( 'order.statuspayment', -1 );
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param int $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return static Order item for chaining method calls
	 */
	public function setStatusPayment( int $status ) : static
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
	 * @return static Order item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If ID is invalid
	 */
	public function setRelatedId( ?string $id ) : static
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
		return (string) $this->get( 'order.sitecode', '' );
	}


	/**
	 * Returns the comment field of the order item.
	 *
	 * @return string Comment for the order
	 */
	public function getComment() : string
	{
		return (string) $this->get( 'order.comment', '' );
	}


	/**
	 * Sets the comment field of the order item
	 *
	 * @param string|null $comment Comment for the order
	 * @return static Order base item for chaining method calls
	 */
	public function setComment( ?string $comment ) : static
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
		return (string) $this->get( 'order.customerid', '' );
	}


	/**
	 * Sets the customer ID of the customer who has ordered.
	 *
	 * @param string|null $customerid Unique ID of the customer
	 * @return static Order base item for chaining method calls
	 */
	public function setCustomerId( ?string $customerid ) : static
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
		return (string) $this->get( 'order.customerref', '' );
	}


	/**
	 * Sets the customer reference field of the order item
	 *
	 * @param string|null $value Customer reference for the order
	 * @return static Order base item for chaining method calls
	 */
	public function setCustomerReference( ?string $value ) : static
	{
		return $this->set( 'order.customerref', (string) $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Order item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.channel': !$private ?: $item->setChannel( $value ? (string) $value : null ); break;
				case 'order.invoiceno': !$private ?: $item->setInvoiceNumber( $value ? (string) $value : null ); break;
				case 'order.statusdelivery': !$private ?: $item->setStatusDelivery( (int) $value ); break;
				case 'order.statuspayment': !$private ?: $item->setStatusPayment( (int) $value ); break;
				case 'order.datedelivery': !$private ?: $item->setDateDelivery( $value ? (string) $value : null ); break;
				case 'order.datepayment': !$private ?: $item->setDatePayment( $value ? (string) $value : null ); break;
				case 'order.customerid': !$private ?: $item->setCustomerId( $value ? (string) $value : null ); break;
				case 'order.customerref': $item->setCustomerReference( $value ? (string) $value : null ); break;
				case 'order.languageid': $item->locale()->setLanguageId( $value ? (string) $value : null ); break;
				case 'order.relatedid': $item->setRelatedId( $value ? (string) $value : null ); break;
				case 'order.comment': $item->setComment( $value ? (string) $value : null ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
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
		$list['order.languageid'] = $this->locale()->getLanguageId();
		$list['order.customerref'] = $this->getCustomerReference();
		$list['order.comment'] = $this->getComment();

		return $list;
	}
}
