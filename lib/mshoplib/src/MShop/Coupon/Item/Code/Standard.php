<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getParentId() : ?string
	{
		return $this->get( 'coupon.code.parentid' );
	}


	/**
	 * Sets the new unique ID of the coupon item the code belongs to.
	 *
	 * @param string|null $id Unique ID of the coupon item
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setParentId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.code.parentid', $id );
	}


	/**
	 * Returns the code of the coupon item.
	 *
	 * @return string|null Coupon code
	 */
	public function getCode() : ?string
	{
		return $this->get( 'coupon.code.code' );
	}


	/**
	 * Sets the new code for the coupon item.
	 *
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Coupon\Item\Code\Iface
	{
		return $this->set( 'coupon.code.code', $this->checkCode( $code ) );
	}


	/**
	 * Returns the number of tries the code is valid.
	 *
	 * @return int|null Number of available tries or null for unlimited
	 */
	public function getCount() : ?int
	{
		if( ( $result = $this->get( 'coupon.code.count', 0 ) ) !== null ) {
			return $result;
		}

		return null;
	}


	/**
	 * Sets the new number of tries the code is valid.
	 *
	 * @param int|null $count Number of tries or null for unlimited
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setCount( $count = null ) : \Aimeos\MShop\Coupon\Item\Code\Iface
	{
		return $this->set( 'coupon.code.count', is_numeric( $count ) ? (int) $count : null );
	}


	/**
	 * Returns the starting point of time, in which the code is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'coupon.code.datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the code is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.code.datestart', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the code is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'coupon.code.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the code is available.
	 *
	 * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'coupon.code.dateend', $this->checkDateFormat( $date ) );
	}


	/**
	 * Returns reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @return string Arbitrary value depending on the coupon provider
	 */
	public function getRef() : string
	{
		return $this->get( 'coupon.code.ref', '' );
	}


	/**
	 * Sets the new reference for the coupon code
	 * This can be an arbitrary value used by the coupon provider
	 *
	 * @param string|null $ref Arbitrary value depending on the coupon provider
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function setRef( ?string $ref ) : \Aimeos\MShop\Coupon\Item\Code\Iface
	{
		return $this->set( 'coupon.code.ref', (string) $ref );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'coupon/code';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable()
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->date )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->date );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
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
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
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
