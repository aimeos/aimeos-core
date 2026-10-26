<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Language;


/**
 * Default implementation of a Language item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Locale\Item\Language\Iface
{
	/**
	 * Sets the id of the language.
	 *
	 * @param string|null $key Id to set
	 * @return static Locale language item for chaining method calls
	 */
	public function setId( ?string $key ) : static
	{
		return parent::setId( \Aimeos\Utils::language( $key ) );
	}


	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'locale.language.code', $this->get( 'locale.language.id', '' ) );
	}


	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $code two letter ISO language code
	 * @return static Locale language item for chaining method calls
	 */
	public function setCode( string $code ) : static
	{
		return $this->set( 'locale.language.code', \Aimeos\Utils::language( $code, false ) );
	}


	/**
	 * Returns the label property.
	 *
	 * @return string Returns the label of the language
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'locale.language.label', '' );
	}


	/**
	 * Sets the label property.
	 *
	 * @param string $label Label of the language
	 * @return static Locale language item for chaining method calls
	 */
	public function setLabel( string $label ) : static
	{
		return $this->set( 'locale.language.label', (string) $label );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'locale.language.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $status Status of the item
	 * @return static Locale language item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'locale.language.status', $status );
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
	 * @return static Language item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.language.code': $item->setCode( (string) $value ); break;
				case 'locale.language.label': $item->setLabel( (string) $value ); break;
				case 'locale.language.status': $item->setStatus( (int) $value ); break;
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

		$list['locale.language.code'] = $this->getCode();
		$list['locale.language.label'] = $this->getLabel();
		$list['locale.language.status'] = $this->getStatus();

		return $list;
	}
}
