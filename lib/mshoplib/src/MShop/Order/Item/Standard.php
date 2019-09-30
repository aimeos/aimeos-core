<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Default implementation of an order invoice item.
 *
 * @property integer oldPaymentStatus Last delivery status before it was changed by setDeliveryStatus()
 * @property integer oldDeliveryStatus Last payment status before it was changed by setPaymentStatus()
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base
	implements \Aimeos\MShop\Order\Item\Iface
{
	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'order.', $values );
	}


	/**
	 * Returns the basic order ID.
	 *
	 * @return string|null Basic order ID
	 */
	public function getBaseId()
	{
		return $this->get( 'order.baseid' );
	}


	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param string $id ID of the basic order item
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setBaseId( $id )
	{
		return $this->set( 'order.baseid', (string) $id );
	}


	/**
	 * Returns the type of the invoice (repeating, web, phone, etc).
	 *
	 * @return string Invoice type
	 */
	public function getType()
	{
		return (string) $this->get( 'order.type', '' );
	}


	/**
	 * Sets the type of the invoice.
	 *
	 * @param string $type Invoice type
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setType( $type )
	{
		return $this->set( 'order.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery()
	{
		return $this->get( 'order.datedelivery' );
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDateDelivery( $date )
	{
		return $this->set( 'order.datedelivery', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the purchase date of the invoice.
	 *
	 * @return string|null ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment()
	{
		return $this->get( 'order.datepayment' );
	}


	/**
	 * Sets the purchase date of the invoice.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDatePayment( $date )
	{
		return $this->set( 'order.datepayment', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getDeliveryStatus()
	{
		return (int) $this->get( 'order.statusdelivery', \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED );
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setDeliveryStatus( $status )
	{
		$this->set( '.statusdelivery', $this->get( 'order.statusdelivery' ) );
		return $this->set( 'order.statusdelivery', (int) $status );
	}


	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getPaymentStatus()
	{
		return (int) $this->get( 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED );
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setPaymentStatus( $status )
	{
		if( (int) $status !== $this->getPaymentStatus() ) {
			$this->set( 'order.datepayment', date( 'Y-m-d H:i:s' ) );
		}

		$this->set( '.statuspayment', $this->get( 'order.statuspayment' ) );
		return $this->set( 'order.statuspayment', (int) $status );
	}


	/**
	 * Returns the related invoice ID.
	 *
	 * @return string|null Related invoice ID
	 */
	public function getRelatedId()
	{
		return $this->get( 'order.relatedid' );
	}


	/**
	 * Sets the related invoice ID.
	 *
	 * @param string|null $id Related invoice ID
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If ID is invalid
	 */
	public function setRelatedId( $id )
	{
		return $this->set( 'order.relatedid', $id );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.baseid': !$private ?: $item = $item->setBaseId( $value ); break;
				case 'order.type': $item = $item->setType( $value ); break;
				case 'order.statusdelivery': $item = $item->setDeliveryStatus( $value ); break;
				case 'order.statuspayment': $item = $item->setPaymentStatus( $value ); break;
				case 'order.datepayment': $item = $item->setDatePayment( $value ); break;
				case 'order.datedelivery': $item = $item->setDateDelivery( $value ); break;
				case 'order.relatedid': $item = $item->setRelatedId( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['order.type'] = $this->getType();
		$list['order.statusdelivery'] = $this->getDeliveryStatus();
		$list['order.statuspayment'] = $this->getPaymentStatus();
		$list['order.datepayment'] = $this->getDatePayment();
		$list['order.datedelivery'] = $this->getDateDelivery();
		$list['order.relatedid'] = $this->getRelatedId();

		if( $private === true ) {
			$list['order.baseid'] = $this->getBaseId();
		}

		return $list;
	}
}
