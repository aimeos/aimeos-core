<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Item\Code;


/**
 * Default coupon code implementation.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Coupon\Item\Code\Iface
{
	private $values;

	/**
	 * Initializes the coupon code instance
	 *
	 * @param array $values Associative array with ID, coupon item ID, code and counter
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'coupon.code.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the unique ID of the coupon item the code belongs to.
	 *
	 * @return integer Unique ID of the coupon item
	 */
	public function getParentId()
	{
		if( isset( $this->values['coupon.code.parentid'] ) ) {
			return (int) $this->values['coupon.code.parentid'];
		}

		return null;
	}


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param integer $id Unique ID of the coupon item
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setParentId( $id )
	{
		if( $id == $this->getParentId() ) { return $this; }

		$this->values['coupon.code.parentid'] = (int) $id;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string Coupon code
	 */
	public function getCode()
	{
		if( isset( $this->values['coupon.code.code'] ) ) {
			return (string) $this->values['coupon.code.code'];
		}

		return null;
	}


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getCode() ) { return $this; }

		$this->values['coupon.code.code'] = (string) $code;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return integer Number of available tries
	 */
	public function getCount()
	{
		if( isset( $this->values['coupon.code.count'] ) ) {
			return (int) $this->values['coupon.code.count'];
		}

		return 0;
	}


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param integer $count Number of tries
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCount( $count )
	{
		if( $count == $this->getCount() ) { return $this; }

		$this->values['coupon.code.count'] = (string) $count;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		if( isset( $this->values['coupon.code.datestart'] ) ) {
			return (string) $this->values['coupon.code.datestart'];
		}

		return null;
	}


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		if( $date == $this->getDateStart() ) { return $this; }

		$this->values['coupon.code.datestart'] = $this->checkDateFormat( $date );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		if( isset( $this->values['coupon.code.dateend'] ) ) {
			return (string) $this->values['coupon.code.dateend'];
		}

		return null;
	}


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( $date == $this->getDateEnd() ) { return $this; }

		$this->values['coupon.code.dateend'] = $this->checkDateFormat( $date );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'coupon/code';
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
				case 'coupon.code.count': $this->setCount( $value ); break;
				case 'coupon.code.code': $this->setCode( $value ); break;
				case 'coupon.code.parentid': $this->setParentId( $value ); break;
				case 'coupon.code.datestart': $this->setDateStart( $value ); break;
				case 'coupon.code.dateend': $this->setDateEnd( $value ); break;
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

		$list['coupon.code.count'] = $this->getCount();
		$list['coupon.code.code'] = $this->getCode();
		$list['coupon.code.parentid'] = $this->getParentId();
		$list['coupon.code.datestart'] = $this->getDateStart();
		$list['coupon.code.dateend'] = $this->getDateEnd();

		return $list;
	}

}
