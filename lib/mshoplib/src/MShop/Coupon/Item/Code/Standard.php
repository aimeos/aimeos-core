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
		return ( isset( $this->values['parentid'] ) ? (int) $this->values['parentid'] : null );
	}


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param integer $id Unique ID of the coupon item
	 */
	public function setParentId( $id )
	{
		if( $id == $this->getParentId() ) { return; }

		$this->values['parentid'] = (int) $id;
		$this->setModified();
	}


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string Coupon code
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : null );
	}


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 */
	public function setCode( $code )
	{
		$this->checkCode( $code );

		if( $code == $this->getCode() ) { return; }

		$this->values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return integer Number of available tries
	 */
	public function getCount()
	{
		return ( isset( $this->values['count'] ) ? (int) $this->values['count'] : 0 );
	}


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param integer $count Number of tries
	 */
	public function setCount( $count )
	{
		if( $count == $this->getCount() ) { return; }

		$this->values['count'] = (string) $count;
		$this->setModified();
	}


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return ( isset( $this->values['start'] ) ? (string) $this->values['start'] : null );
	}


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date )
	{
		if( $date == $this->getDateStart() ) { return; }

		$this->values['start'] = $this->checkDateFormat( $date );

		$this->setModified();
	}


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return ( isset( $this->values['end'] ) ? (string) $this->values['end'] : null );
	}


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date )
	{
		if( $date == $this->getDateEnd() ) { return; }

		$this->values['end'] = $this->checkDateFormat( $date );

		$this->setModified();
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
