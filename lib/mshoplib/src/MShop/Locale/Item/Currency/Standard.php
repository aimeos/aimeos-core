<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * Initializes the currency object object.
	 *
	 * @param array $values Possible params to be set on initialization
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'locale.currency.', $values );
	}


	/**
	 * Sets the ID of the currency.
	 *
	 * @param string|null $key ID of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setId( ?string $key ) : \Aimeos\MShop\Common\Item\Iface
	{
		return parent::setId( $this->checkCurrencyId( $key ) );
	}


	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode() : string
	{
		return $this->get( 'locale.currency.code', $this->get( 'locale.currency.id', '' ) );
	}


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $code Code of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'locale.currency.code', $this->checkCurrencyId( $code, false ) );
	}


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel() : string
	{
		return $this->get( 'locale.currency.label', '' );
	}


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Locale\Item\Currency\Iface
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
		return $this->get( 'locale.currency.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'locale.currency.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'locale/currency';
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
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Currency item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.currency.code': $item = $item->setCode( $value ); break;
				case 'locale.currency.label': $item = $item->setLabel( $value ); break;
				case 'locale.currency.status': $item = $item->setStatus( (int) $value ); break;
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

		$list['locale.currency.code'] = $this->getCode();
		$list['locale.currency.label'] = $this->getLabel();
		$list['locale.currency.status'] = $this->getStatus();

		return $list;
	}
}
