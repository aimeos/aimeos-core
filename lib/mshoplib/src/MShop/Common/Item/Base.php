<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item;


/**
 * Common methods for all item objects.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MW\Common\Item\Base
	implements \Aimeos\MShop\Common\Item\Iface
{
	private $bdata;
	private $prefix;
	private $available = true;
	private $modified = false;


	/**
	 * Initializes the class properties.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 */
	public function __construct( $prefix, array $values )
	{
		$this->prefix = (string) $prefix;
		$this->bdata = $values;
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
	}


	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @return mixed|null Property value or null if property is unknown
	 */
	public function __get( $name )
	{
		if( isset( $this->bdata[$name] ) ) {
			return $this->bdata[$name];
		}
	}


	/**
	 * Tests if the item property for the given name is available
	 *
	 * @param string $name Name of the property
	 * @return boolean True if the property exists, false if not
	 */
	public function __isset( $name )
	{
		if( array_key_exists( $name, $this->bdata ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId()
	{
		$key = $this->prefix . 'id';

		if( isset( $this->bdata[$key] ) && $this->bdata[$key] != '' ) {
			return (string) $this->bdata[$key];
		}

		if( isset( $this->bdata['id'] ) && $this->bdata['id'] != '' ) {
			return (string) $this->bdata['id'];
		}
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setId( $id )
	{
		$key = $this->prefix . 'id';

		if( ( $this->bdata[$key] = \Aimeos\MShop\Common\Item\Base::checkId( $this->getId(), $id ) ) === null ) {
			$this->modified = true;
		} else {
			$this->modified = false;
		}

		$this->bdata['id'] = $this->bdata[$key];
		return $this;
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return string|null Site ID or null if no site id is available
	 */
	public function getSiteId()
	{
		$key = $this->prefix . 'siteid';

		if( isset( $this->bdata[$key] ) ) {
			return (string) $this->bdata[$key];
		}

		if( isset( $this->bdata['siteid'] ) ) {
			return (string) $this->bdata['siteid'];
		}
	}


	/**
	 * Returns modify date/time of the order coupon.
	 *
	 * @return string|null Modification time (YYYY-MM-DD HH:mm:ss)
	 */
	public function getTimeModified()
	{
		$key = $this->prefix . 'mtime';

		if( isset( $this->bdata[$key] ) ) {
			return (string) $this->bdata[$key];
		}

		if( isset( $this->bdata['mtime'] ) ) {
			return (string) $this->bdata['mtime'];
		}
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		$key = $this->prefix . 'ctime';

		if( isset( $this->bdata[$key] ) ) {
			return (string) $this->bdata[$key];
		}

		if( isset( $this->bdata['ctime'] ) ) {
			return (string) $this->bdata['ctime'];
		}
	}


	/**
	 * Returns the name of editor who created/modified the item at last.
	 *
	 * @return string Name of editor who created/modified the item at last
	 */
	public function getEditor()
	{
		$key = $this->prefix . 'editor';

		if( isset( $this->bdata[$key] ) ) {
			return (string) $this->bdata[$key];
		}

		if( isset( $this->bdata['editor'] ) ) {
			return (string) $this->bdata['editor'];
		}

		return '';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return $this->available;
	}


	/**
	 * Sets the general availability of the item
	 *
	 * @return boolean $value True if available, false if not
	 */
	public function setAvailable( $value )
	{
		$this->available = (bool) $value;
	}


	/**
	 * Tests if this Item object was modified.
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified()
	{
		return $this->modified;
	}


	/**
	 * Sets the modified flag of the object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setModified()
	{
		$this->modified = true;
		return $this;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		if( array_key_exists( $this->prefix . 'id', $list ) )
		{
			$this->setId( $list[$this->prefix . 'id'] );
			unset( $list[$this->prefix . 'id'] );
		}

		unset( $list[$this->prefix . 'siteid'] );
		unset( $list[$this->prefix . 'ctime'] );
		unset( $list[$this->prefix . 'mtime'] );
		unset( $list[$this->prefix . 'editor'] );

		return $list;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = [$this->prefix . 'id' => $this->getId()];

		if( $private === true )
		{
			$list[$this->prefix . 'siteid'] = $this->getSiteId();
			$list[$this->prefix . 'ctime'] = $this->getTimeCreated();
			$list[$this->prefix . 'mtime'] = $this->getTimeModified();
			$list[$this->prefix . 'editor'] = $this->getEditor();
		}

		return $list;
	}


	/**
	 * Checks if the new ID is valid for the item.
	 *
	 * @param string $old Current ID of the item
	 * @param string $new New ID which should be set in the item
	 * @return string Value of the new ID
	 * @throws \Aimeos\MShop\Common\Exception if the ID is not null or not the same as the old one
	 */
	public static function checkId( $old, $new )
	{
		if( $new != null && $old != null && $old != $new ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'New ID "%1$s" for item differs from old ID "%2$s"', $new, $old ) );
		}

		return $new;
	}


	/**
	 * Tests if the date parameter represents an ISO format.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format or null
	 * @return string|null Clean date or null for no date
	 * @throws \Aimeos\MShop\Exception If the date is invalid
	 */
	protected function checkDateFormat( $date )
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9](( |T)[0-2][0-9]:[0-5][0-9](:[0-5][0-9])?)?$/';

		if( $date != null )
		{
			if( preg_match( $regex, (string) $date ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD hh:mm:ss" expected.', $date ) );
			}

			return str_replace( 'T', ' ', (string) $date );
		}
	}


	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @return string Item code
	 * @throws \Aimeos\MShop\Exception If the code is invalid
	 */
	protected function checkCode( $code )
	{
		if( strlen( $code ) > 32 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Code "%1$s" must not be longer than 32 characters', $code ) );
		}

		return (string) $code;
	}


	/**
	 * Tests if the country ID parameter represents an ISO country format.
	 *
	 * @param string|null $countryid Two letter ISO country format, e.g. DE
	 * @param boolean $null True if null is allowed, false if not
	 * @return string|null Two letter ISO country ID or null for no country
	 * @throws \Aimeos\MShop\Exception If the country ID is invalid
	 */
	protected function checkCountryId( $countryid, $null = true )
	{
		if( $null === false && $countryid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO country code "%1$s"', '<null>' ) );
		}

		if( $countryid != null )
		{
			if( preg_match( '/^[A-Za-z]{2}$/', $countryid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO country code "%1$s"', $countryid ) );
			}

			return strtoupper( $countryid );
		}
	}


	/**
	 * Tests if the currency ID parameter represents an ISO currency format.
	 *
	 * @param string|null $currencyid Three letter ISO currency format, e.g. EUR
	 * @param boolean $null True if null is allowed, false if not
	 * @return string|null Three letter ISO currency ID or null for no currency
	 * @throws \Aimeos\MShop\Exception If the currency ID is invalid
	 */
	protected function checkCurrencyId( $currencyid, $null = true )
	{
		if( $null === false && $currencyid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code "%1$s"', '<null>' ) );
		}

		if( $currencyid != null )
		{
			if( preg_match( '/^[A-Z]{3}$/', $currencyid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code "%1$s"', $currencyid ) );
			}

			return strtoupper( $currencyid );
		}
	}


	/**
	 * Tests if the language ID parameter represents an ISO language format.
	 *
	 * @param string|null $langid ISO language format, e.g. de or de_DE
	 * @param boolean $null True if null is allowed, false if not
	 * @return string|null ISO language ID or null for no language
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	protected function checkLanguageId( $langid, $null = true )
	{
		if( $null === false && $langid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO language code "%1$s"', '<null>' ) );
		}

		if( $langid != null )
		{
			if( preg_match( '/^[a-zA-Z]{2}(_[a-zA-Z]{2})?$/', $langid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO language code "%1$s"', $langid ) );
			}

			$parts = explode( '_', $langid );
			$parts[0] = strtolower( $parts[0] );

			if( isset( $parts[1] ) ) {
				$parts[1] = strtoupper( $parts[1] );
			}

			return implode( '_', $parts );
		}
	}


	/**
	 * Returns the raw value list.
	 *
	 * @return array Associative list of key/value pairs
	 */
	protected function getRawValues()
	{
		return $this->bdata;
	}
}
