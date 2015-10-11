<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	private $values;
	private $oldPaymentStatus = \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED;
	private $oldDeliveryStatus = \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED;


	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'order.', $values );

		$this->values = $values;

		if( !isset( $values['datepayment'] ) ) {
			$this->values['datepayment'] = date( 'Y-m-d H:i:s', time() );
		}

		if( isset( $values['statuspayment'] ) ) {
			$this->oldPaymentStatus = (int) $values['statuspayment'];
		}

		if( isset( $values['statusdelivery'] ) ) {
			$this->oldDeliveryStatus = (int) $values['statusdelivery'];
		}
	}


	/**
	 * Returns the basic order ID.
	 *
	 * @return integer Basic order ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->values['baseid'] ) ? (int) $this->values['baseid'] : null );
	}


	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param integer $id ID of the basic order item
	 */
	public function setBaseId( $id )
	{
		if( $id == $this->getBaseId() ) { return; }

		$this->values['baseid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the type of the invoice (repeating, web, phone, etc).
	 *
	 * @return string Invoice type
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : '' );
	}


	/**
	 * Sets the type of the invoice.
	 *
	 * @param string $type Invoice type
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return; }

		$this->checkType( $type );

		$this->values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery()
	{
		return ( isset( $this->values['datedelivery'] ) ? (string) $this->values['datedelivery'] : null );
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function setDateDelivery( $date )
	{
		if( $date === $this->getDateDelivery() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['datedelivery'] = (string) $date;
		$this->setModified();
	}


	/**
	 * Returns the purchase date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment()
	{
		return ( isset( $this->values['datepayment'] ) ? (string) $this->values['datepayment'] : null );
	}


	/**
	 * Sets the purchase date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function setDatePayment( $date )
	{
		if( $date === $this->getDatePayment() ) { return; }

		$this->checkDateFormat( $date );

		$this->values['datepayment'] = (string) $date;
		$this->setModified();
	}


	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getDeliveryStatus()
	{
		if( isset( $this->values['statusdelivery'] ) ) {
			return (int) $this->values['statusdelivery'];
		}

		return \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED;
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function setDeliveryStatus( $status )
	{
		$this->values['statusdelivery'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function getPaymentStatus()
	{
		if( isset( $this->values['statuspayment'] ) ) {
			return (int) $this->values['statuspayment'];
		}

		return \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED;
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from \Aimeos\MShop\Order\Item\Base
	 */
	public function setPaymentStatus( $status )
	{
		$this->values['statuspayment'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the related invoice ID.
	 *
	 * @param integer|null Related invoice ID
	 */
	public function getRelatedId()
	{
		return ( isset( $this->values['relatedid'] ) ? (int) $this->values['relatedid'] : null );
	}


	/**
	 * Sets the related invoice ID.
	 *
	 * @param integer|null $id Related invoice ID
	 * @throws \Aimeos\MShop\Order\Exception If ID is invalid
	 */
	public function setRelatedId( $id )
	{
		if( $id === $this->getRelatedId() ) { return; }
		$id = (int) $id;
		$this->values['relatedid'] = $id;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.baseid': $this->setBaseId( $value ); break;
				case 'order.type': $this->setType( $value ); break;
				case 'order.statusdelivery': $this->setDeliveryStatus( $value ); break;
				case 'order.statuspayment': $this->setPaymentStatus( $value ); break;
				case 'order.datepayment': $this->setDatePayment( $value ); break;
				case 'order.datedelivery': $this->setDateDelivery( $value ); break;
				case 'order.relatedid': $this->setRelatedId( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['order.baseid'] = $this->getBaseId();
		$list['order.type'] = $this->getType();
		$list['order.statusdelivery'] = $this->getDeliveryStatus();
		$list['order.statuspayment'] = $this->getPaymentStatus();
		$list['order.datepayment'] = $this->getDatePayment();
		$list['order.datedelivery'] = $this->getDateDelivery();
		$list['order.relatedid'] = $this->getRelatedId();

		return $list;
	}


	public function __get( $name )
	{
		switch( $name )
		{
			case 'oldPaymentStatus':
				return $this->oldPaymentStatus;
			case 'oldDeliveryStatus':
				return $this->oldDeliveryStatus;
			default:
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Property name "%1$s" not within allowed range', $name ) );
		}
	}
}
