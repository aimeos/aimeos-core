<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $baseItem;


	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 * @param \Aimeos\MShop\Order\Item\Base\Iface|null $baseItem Order basket if available
	 */
	public function __construct( array $values = [], ?\Aimeos\MShop\Order\Item\Base\Iface $baseItem = null )
	{
		parent::__construct( 'order.', $values );
		$this->baseItem = $baseItem;
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
	 * Returns the associated order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface|null Order base item
	 */
	public function getBaseItem() : ?\Aimeos\MShop\Order\Item\Base\Iface
	{
		return $this->baseItem;
	}


	/**
	 * Sets the associated order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item
	 */
	public function setBaseItem( \Aimeos\MShop\Order\Item\Base\Iface $baseItem ) : \Aimeos\MShop\Order\Item\Iface
	{
		$this->baseItem = $baseItem;
		return $this;
	}


	/**
	 * Returns the basic order ID.
	 *
	 * @return string|null Basic order ID
	 */
	public function getBaseId() : ?string
	{
		return $this->get( 'order.baseid' );
	}


	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param string $id ID of the basic order item
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setBaseId( string $id ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->set( 'order.baseid', $id );
	}


	/**
	 * Returns the type of the invoice (repeating, web, phone, etc).
	 *
	 * @return string Invoice type
	 */
	public function getType() : string
	{
		return $this->get( 'order.type', '' );
	}


	/**
	 * Sets the type of the invoice.
	 *
	 * @param string $type Invoice type
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'order.type', $this->checkCode( $type ) );
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
	public function getStatusDelivery() : ?int
	{
		return $this->get( 'order.statusdelivery' );
	}


	/**
	 * @deprecated 2022.01
	 */
	public function getDeliveryStatus() : ?int
	{
		return $this->getStatusDelivery();
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param int|null $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusDelivery( ?int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $status !== null ) {
			$this->set( '.statusdelivery', $this->get( 'order.statusdelivery' ) );
		}

		return $this->set( 'order.statusdelivery', $status );
	}


	/**
	 * @deprecated 2022.01
	 */
	public function setDeliveryStatus( ?int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->setStatusDelivery( $status );
	}


	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return int Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getStatusPayment() : ?int
	{
		return $this->get( 'order.statuspayment' );
	}


	/**
	 * @deprecated 2022.01
	 */
	public function getPaymentStatus() : ?int
	{
		return $this->getStatusPayment();
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param int|null $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 * @return \Aimeos\MShop\Order\Item\Iface Order item for chaining method calls
	 */
	public function setStatusPayment( ?int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		if( $status !== $this->getStatusPayment() ) {
			$this->set( 'order.datepayment', date( 'Y-m-d H:i:s' ) );
		}

		if( $status !== null ) {
			$this->set( '.statuspayment', $this->get( 'order.statuspayment' ) );
		}

		return $this->set( 'order.statuspayment', $status );
	}


	/**
	 * @deprecated 2022.01
	 */
	public function setPaymentStatus( ?int $status ) : \Aimeos\MShop\Order\Item\Iface
	{
		return $this->setStatusPayment( $status );
	}


	/**
	 * Returns the related invoice ID.
	 *
	 * @return string Related invoice ID
	 */
	public function getRelatedId() : string
	{
		return $this->get( 'order.relatedid', '' );
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

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.baseid': !$private ?: $item = $item->setBaseId( $value ); break;
				case 'order.type': $item = $item->setType( $value ); break;
				case 'order.statusdelivery': $item = $item->setStatusDelivery( is_numeric( $value ) ? (int) $value : null ); break;
				case 'order.statuspayment': $item = $item->setStatusPayment( is_numeric( $value ) ? (int) $value : null ); break;
				case 'order.datedelivery': $item = $item->setDateDelivery( $value ); break;
				case 'order.datepayment': $item = $item->setDatePayment( $value ); break;
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
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['order.type'] = $this->getType();
		$list['order.statusdelivery'] = $this->getStatusDelivery();
		$list['order.statuspayment'] = $this->getStatusPayment();
		$list['order.datedelivery'] = $this->getDateDelivery();
		$list['order.datepayment'] = $this->getDatePayment();
		$list['order.relatedid'] = $this->getRelatedId();

		if( $private === true ) {
			$list['order.baseid'] = $this->getBaseId();
		}

		return $list;
	}
}
