<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * Initialize the language object.
	 *
	 * @param array $values Possible params to be set on init.
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'locale.language.', $values );
	}


	/**
	 * Sets the id of the language.
	 *
	 * @param string $key Id to set
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setId( $key )
	{
		return parent::setId( $this->checkLanguageId( $key ) );
	}


	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode()
	{
		return (string) $this->get( 'locale.language.code', $this->get( 'locale.language.id', '' ) );
	}


	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $code two letter ISO language code
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setCode( $code )
	{
		return $this->set( 'locale.language.code', $this->checkLanguageId( $code, false ) );
	}


	/**
	 * Returns the label property.
	 *
	 * @return string Returns the label of the language
	 */
	public function getLabel()
	{
		return (string) $this->get( 'locale.language.label', '' );
	}


	/**
	 * Sets the label property.
	 *
	 * @param string $label Label of the language
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setLabel( $label )
	{
		return $this->set( 'locale.language.label', (string) $label );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return (int) $this->get( 'locale.language.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setStatus( $status )
	{
		return $this->set( 'locale.language.status', (int) $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'locale/language';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Language item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'locale.language.code': $item = $item->setCode( $value ); break;
				case 'locale.language.label': $item = $item->setLabel( $value ); break;
				case 'locale.language.status': $item = $item->setStatus( $value ); break;
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

		$list['locale.language.code'] = $this->getCode();
		$list['locale.language.label'] = $this->getLabel();
		$list['locale.language.status'] = $this->getStatus();

		return $list;
	}
}
