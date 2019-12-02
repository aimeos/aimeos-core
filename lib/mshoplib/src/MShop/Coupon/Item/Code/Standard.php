<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	private $date;


	/**
	 * Initializes the coupon code instance
	 *
	 * @param array $values Associative array with ID, coupon item ID, code and counter
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'coupon.code.', $values );

		$this->date = isset( $values['.date'] ) ? $values['.date'] : date( 'Y-m-d H:i:s' );
	}


	/**
	 * Returns the unique ID of the coupon item the code belongs to.
	 *
	 * @return string|null Unique ID of the coupon item
	 */
	public function getParentId()
	{
		return $this->get( 'coupon.code.parentid' );
	}


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param string $id Unique ID of the coupon item
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setParentId( $id )
	{
		return $this->set( 'coupon.code.parentid', (string) $id );
	}


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string|null Coupon code
	 */
	public function getCode()
	{
		return $this->get( 'coupon.code.code' );
	}


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCode( $code )
	{
		return $this->set( 'coupon.code.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return integer|null Number of available tries or null for unlimited
	 */
	public function getCount()
	{
		if( ( $result = $this->get( 'coupon.code.count', 0 ) ) !== null ) {
			return (int) $result;
		}

		return null;
	}


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param integer|null $count Number of tries or null for unlimited
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCount( $count )
	{
		$count = ( is_numeric( $count ) ? (int) $count : null );
		return $this->set( 'coupon.code.count', $count );
	}


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return $this->get( 'coupon.code.datestart' );
	}


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		return $this->set( 'coupon.code.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return $this->get( 'coupon.code.dateend' );
	}


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		return $this->set( 'coupon.code.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @return string Arbitrary value depending on the coupon provider
	 */
	public function getRef()
	{
		return (string) $this->get( 'coupon.code.ref', '' );
	}


	/**
	 * Sets the new reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @param string $ref Arbitrary value depending on the coupon provider
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setRef( $ref )
	{
		return $this->set( 'coupon.code.ref', (string) $ref );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'coupon/code';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable()
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->date )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->date );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'coupon.code.parentid': !$private ?: $item = $item->setParentId( $value ); break;
				case 'coupon.code.datestart': $item = $item->setDateStart( $value ); break;
				case 'coupon.code.dateend': $item = $item->setDateEnd( $value ); break;
				case 'coupon.code.count': $item = $item->setCount( $value ); break;
				case 'coupon.code.code': $item = $item->setCode( $value ); break;
				case 'coupon.code.ref': $item = $item->setRef( $value ); break;
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

		$list['coupon.code.datestart'] = $this->getDateStart();
		$list['coupon.code.dateend'] = $this->getDateEnd();
		$list['coupon.code.count'] = $this->getCount();
		$list['coupon.code.code'] = $this->getCode();
		$list['coupon.code.ref'] = $this->getRef();

		if( $private === true ) {
			$list['coupon.code.parentid'] = $this->getParentId();
		}

		return $list;
	}

}
