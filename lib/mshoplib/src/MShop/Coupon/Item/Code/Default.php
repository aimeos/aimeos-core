<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Coupon
 * @version $Id: Default.php 115 2012-10-09 16:54:17Z fblasel $
 */


/**
 * Default coupon code implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class MShop_Coupon_Item_Code_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Coupon_Item_Code_Interface
{
	private $_values;

	/**
	 * Initializes the coupon code instance
	 *
	 * @param array $values Associative array with ID, coupon item ID, code and counter
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('coupon.code.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the unique ID of the coupon item the code belongs to.
	 *
	 * @return integer Unique ID of the coupon item
	 */
	public function getCouponId()
	{
		return ( isset( $this->_values['couponid'] ) ? (int) $this->_values['couponid'] : null );
	}


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param integer $id Unique ID of the coupon item
	 */
	public function setCouponId( $id )
	{
		if ( $id == $this->getCouponId() ) { return; }

		$this->_values['couponid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string Coupon code
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : null );
	}


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return integer Number of available tries
	 */
	public function getCount()
	{
		return ( isset( $this->_values['count'] ) ? (int) $this->_values['count'] : 0 );
	}


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param integer $count Number of tries
	 */
	public function setCount( $count )
	{
		if ( $count == $this->getCount() ) { return; }

		$this->_values['count'] = (string) $count;
		$this->setModified();
	}


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return ( isset( $this->_values['start'] ) ? (string) $this->_values['start'] : null );
	}


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date )
	{
		if ( $date == $this->getDateStart() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['start'] = ( $date !== null ? (string) $date : null );

		$this->setModified();
	}


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return ( isset( $this->_values['end'] ) ? (string) $this->_values['end'] : null );
	}


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date )
	{
		if ( $date == $this->getDateEnd() ) { return; }

		$this->_checkDateFormat($date);

		$this->_values['end'] = ( $date !== null ? (string) $date : null );

		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['coupon.code.count'] = $this->getCount();
		$list['coupon.code.code'] = $this->getCode();
		$list['coupon.code.couponid'] = $this->getCouponId();
		$list['coupon.code.datestart'] = $this->getDateStart();
		$list['coupon.code.dateend'] = $this->getDateEnd();

		return $list;
	}

}
