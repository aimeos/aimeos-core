<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Currency;


/**
 * Default implementation of a currency item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Locale\Item\Currency\Iface
{
	/**
	 * Sets the ID of the currency.
	 *
	 * @param string|null $key ID of the currency
	 * @return static Locale currency item for chaining method calls
	 */
	public function setId( ?string $key ) : static
	{
		return parent::setId( \Aimeos\Utils::currency( $key ) );
	}


	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'locale.currency.code', $this->get( 'locale.currency.id', '' ) );
	}


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $code Code of the currency
	 * @return static Locale currency item for chaining method calls
	 */
	public function setCode( string $code ) : static
	{
		return $this->set( 'locale.currency.code', \Aimeos\Utils::currency( $code, false ) );
	}


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'locale.currency.label', '' );
	}


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 * @return static Locale currency item for chaining method calls
	 */
	public function setLabel( string $label ) : static
	{
		return $this->set( 'locale.currency.label', $label );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'locale.currency.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $status Status of the item
	 * @return static Locale currency item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'locale.currency.status', $status );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Currency item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.currency.code': $item->setCode( (string) $value ); break;
				case 'locale.currency.label': $item->setLabel( (string) $value ); break;
				case 'locale.currency.status': $item->setStatus( (int) $value ); break;
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

		$list['locale.currency.code'] = $this->getCode();
		$list['locale.currency.label'] = $this->getLabel();
		$list['locale.currency.status'] = $this->getStatus();

		return $list;
	}
}
