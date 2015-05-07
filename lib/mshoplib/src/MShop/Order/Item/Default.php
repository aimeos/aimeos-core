<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default implementation of an order invoice item.
 *
 * @property integer oldPaymentStatus Last delivery status before it was changed by setDeliveryStatus()
 * @property integer oldDeliveryStatus Last payment status before it was changed by setPaymentStatus()
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Default
	extends MShop_Order_Item_Abstract
	implements MShop_Order_Item_Interface
{
	private $_values;
	private $_oldPaymentStatus = MShop_Order_Item_Abstract::PAY_UNFINISHED;
	private $_oldDeliveryStatus = MShop_Order_Item_Abstract::STAT_UNFINISHED;


	/**
	 * Initializes the object with the given values.
	 *
	 * @param array $values Associative list of values from database
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('order.', $values);

		$this->_values = $values;

		if ( !isset($values['datepayment']) ) {
			$this->_values['datepayment'] = date( 'Y-m-d H:i:s', time() );
		}

		if( isset( $values['statuspayment'] ) ) {
			$this->_oldPaymentStatus = (int) $values['statuspayment'];
		}

		if( isset( $values['statusdelivery'] ) ) {
			$this->_oldDeliveryStatus = (int) $values['statusdelivery'];
		}
	}


	/**
	 * Returns the basic order ID.
	 *
	 * @return integer Basic order ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the ID of the basic order item which contains the order details.
	 *
	 * @param integer $id ID of the basic order item
	 */
	public function setBaseId( $id )
	{
		if ( $id == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the type of the invoice (repeating, web, phone, etc).
	 *
	 * @return string Invoice type
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : '' );
	}


	/**
	 * Sets the type of the invoice.
	 *
	 * @param string $type Invoice type
	 */
	public function setType( $type )
	{
		if ( $type == $this->getType() ) { return; }

		$this->_checkType($type);

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Returns the delivery date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDateDelivery()
	{
		return ( isset( $this->_values['datedelivery'] ) ? (string) $this->_values['datedelivery'] : null );
	}


	/**
	 * Sets the delivery date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function setDateDelivery( $date )
	{
		if ( $date === $this->getDateDelivery() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['datedelivery'] = (string) $date;
		$this->setModified();
	}


	/**
	 * Returns the purchase date of the invoice.
	 *
	 * @return string ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function getDatePayment()
	{
		return ( isset( $this->_values['datepayment'] ) ? (string) $this->_values['datepayment'] : null );
	}


	/**
	 * Sets the purchase date of the invoice.
	 *
	 * @param string $date ISO date in yyyy-mm-dd HH:ii:ss format
	 */
	public function setDatePayment( $date )
	{
		if ( $date === $this->getDatePayment() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['datepayment'] = (string) $date;
		$this->setModified();
	}


	/**
	 * Returns the delivery status of the invoice.
	 *
	 * @return integer Status code constant from MShop_Order_Item_Abstract
	 */
	public function getDeliveryStatus()
	{
		if ( isset( $this->_values['statusdelivery'] ) ) {
			return (int) $this->_values['statusdelivery'];
		}

		return MShop_Order_Item_Abstract::STAT_UNFINISHED;
	}


	/**
	 * Sets the delivery status of the invoice.
	 *
	 * @param integer $status Status code constant from MShop_Order_Item_Abstract
	 */
	public function setDeliveryStatus( $status )
	{
		$this->_values['statusdelivery'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the payment status of the invoice.
	 *
	 * @return integer Payment constant from MShop_Order_Item_Abstract
	 */
	public function getPaymentStatus()
	{
		if ( isset( $this->_values['statuspayment'] ) ) {
			return (int) $this->_values['statuspayment'];
		}

		return MShop_Order_Item_Abstract::PAY_UNFINISHED;
	}


	/**
	 * Sets the payment status of the invoice.
	 *
	 * @param integer $status Payment constant from MShop_Order_Item_Abstract
	 */
	public function setPaymentStatus( $status )
	{
		$this->_values['statuspayment'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the related invoice ID.
	 *
	 * @param integer|null Related invoice ID
	 */
	public function getRelatedId()
	{
		return ( isset( $this->_values['relatedid'] ) ? (int) $this->_values['relatedid'] : null );
	}


	/**
	 * Sets the related invoice ID.
	 *
	 * @param integer|null $id Related invoice ID
	 * @throws MShop_Order_Exception If ID is invalid
	 */
	public function setRelatedId( $id )
	{
		if ( $id === $this->getRelatedId() ) { return; }
		$id = (int) $id;
		$this->_values['relatedid'] = $id;
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
				return $this->_oldPaymentStatus;
			case 'oldDeliveryStatus':
				return $this->_oldDeliveryStatus;
			default:
				throw new MShop_Order_Exception( sprintf( 'Property name "%1$s" not within allowed range', $name ) );
		}
	}
}
