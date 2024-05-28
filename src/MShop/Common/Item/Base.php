<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
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
class Base implements \Aimeos\MShop\Common\Item\Iface, \Aimeos\Macro\Iface, \ArrayAccess, \JsonSerializable
{
	use \Aimeos\Macro\Macroable;

	// protected due to PHP serialization
	protected bool $available = true;
	protected bool $modified = false;
	protected string $bprefix;
	protected ?string $type;
	protected array $bdata;


	/**
	 * Initializes the class properties.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 * @param string|null $type Item resource type
	 */
	public function __construct( string $prefix, array $values, string $type = null )
	{
		$this->bprefix = $prefix;
		$this->bdata = $values;
		$this->type = $type;
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
	public function __get( string $name )
	{
		return $this->get( $name );
	}


	/**
	 * Tests if the item property for the given name is available
	 *
	 * @param string $name Name of the property
	 * @return bool True if the property exists, false if not
	 */
	public function __isset( string $name ) : bool
	{
		return array_key_exists( $name, $this->bdata );
	}


	/**
	 * Sets the new item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $value New property value
	 */
	public function __set( string $name, $value )
	{
		$this->set( $name, $value );
	}


	/**
	 * Specifies the data which should be serialized to JSON by json_encode().
	 *
	 * @return array<string,mixed> Data to serialize to JSON
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->bdata;
	}


	/**
	 * Tests if the item property for the given name is available
	 *
	 * @param string $name Name of the property
	 * @return bool True if the property exists, false if not
	 */
	public function offsetExists( $name ) : bool
	{
		return array_key_exists( $name, $this->bdata );
	}


	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @return mixed|null Property value or null if property is unknown
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $name )
	{
		return $this->get( $name );
	}


	/**
	 * Sets the new item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $value New property value
	 */
	public function offsetSet( $name, $value ) : void
	{
		$this->set( $name, $value );
	}


	/**
	 * Removes an item property
	 * This is not supported by items
	 *
	 * @param string $name Name of the property
	 * @throws \LogicException Always thrown because this method isn't supported
	 */
	public function offsetUnset( $name ) : void
	{
		throw new \LogicException( 'Not implemented' );
	}


	/**
	 * Returns the ID of the items
	 *
	 * @return string ID of the item or an empty string
	 */
	public function __toString() : string
	{
		return (string) $this->getId();
	}


	/**
	 * Assigns multiple key/value pairs to the item
	 *
	 * @param iterable $pairs Associative list of key/value pairs
	 * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
	 */
	public function assign( iterable $pairs ) : \Aimeos\MShop\Common\Item\Iface
	{
		foreach( $pairs as $key => $value ) {
			$this->set( $key, $value );
		}

		return $this;
	}


	/**
	 * Returns the item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $default Default value if property is unknown
	 * @return mixed|null Property value or default value if property is unknown
	 */
	public function get( string $name, $default = null )
	{
		if( array_key_exists( $name, $this->bdata ) ) {
			return $this->bdata[$name];
		}

		return $default;
	}


	/**
	 * Sets the new item property for the given name
	 *
	 * @param string $name Name of the property
	 * @param mixed $value New property value
	 * @return \Aimeos\MShop\Common\Item\Iface Item for method chaining
	 */
	public function set( string $name, $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		// workaround for NULL values instead of empty strings and stringified integers from database
		if( !array_key_exists( $name, $this->bdata ) || $this->bdata[$name] != $value
			|| $value === null && $this->bdata[$name] !== null
			|| $value !== null && $this->bdata[$name] === null
		) {
			$this->bdata[$name] = $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the ID of the item if available.
	 *
	 * @return string|null ID of the item
	 */
	public function getId() : ?string
	{
		$key = $this->bprefix . 'id';

		if( isset( $this->bdata[$key] ) && $this->bdata[$key] != '' ) {
			return (string) $this->bdata[$key];
		}

		return null;
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->bdata[$this->bprefix . 'id'] = $id;
		$this->modified = ( $id === null );

		return $this;
	}


	/**
	 * Returns the site ID of the item.
	 *
	 * @return string Site ID or null if no site id is available
	 */
	public function getSiteId() : string
	{
		return $this->get( $this->bprefix . 'siteid', $this->get( 'siteid', '' ) );
	}


	/**
	 * Returns the list site IDs up to the root site item.
	 *
	 * @return array List of site IDs
	 */
	public function getSitePath() : array
	{
		$pos = 0;
		$list = [];
		$siteId = $this->getSiteId();

		while( ( $pos = strpos( $siteId, '.', $pos ) ) !== false ) {
			$list[] = substr( $siteId, 0, ++$pos );
		}

		return $list;
	}


	/**
	 * Returns modify date/time of the order coupon.
	 *
	 * @return string|null Modification time (YYYY-MM-DD HH:mm:ss)
	 */
	public function getTimeModified() : ?string
	{
		return $this->get( $this->bprefix . 'mtime', $this->get( 'mtime' ) );
	}


	/**
	 * Returns the create date of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated() : ?string
	{
		return $this->get( $this->bprefix . 'ctime', $this->get( 'ctime' ) );
	}


	/**
	 * Returns the name of editor who created/modified the item at last.
	 *
	 * @return string Name of editor who created/modified the item at last
	 */
	public function editor() : string
	{
		return $this->get( $this->bprefix . 'editor', $this->get( 'editor', '' ) );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return $this->available;
	}


	/**
	 * Sets the general availability of the item
	 *
	 * @return bool $value True if available, false if not
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setAvailable( bool $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->available = $value;
		return $this;
	}


	/**
	 * Tests if this Item object was modified.
	 *
	 * @return bool True if modified, false if not
	 */
	public function isModified() : bool
	{
		return $this->modified;
	}


	/**
	 * Sets the modified flag of the object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function setModified() : \Aimeos\MShop\Common\Item\Iface
	{
		$this->modified = true;
		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		if( !$this->type )
		{
			$parts = explode( '\\', strtolower( get_class( $this ) ) );
			array_shift( $parts ); array_shift( $parts ); // remove "Aimeos\MShop"
			array_pop( $parts );

			$domain = array_shift( $parts ) ?: 'custom';
			array_shift( $parts ); // remove "item"
			array_unshift( $parts, $domain );

			$this->type = join( '/', $parts );
		}

		return $this->type;
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array $list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Common\Item\Iface Item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( $private && array_key_exists( $this->bprefix . 'id', $list ) )
		{
			$this->setId( $list[$this->bprefix . 'id'] );
			unset( $list[$this->bprefix . 'id'] );
		}

		// Add custom columns
		foreach( $list as $key => $value )
		{
			if( ( $value === null || is_scalar( $value ) ) && strpos( $key, '.' ) === false ) {
				$this->set( $key, $value );
			}
		}

		return $this;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = [$this->bprefix . 'id' => $this->getId()];

		if( $private === true )
		{
			$list[$this->bprefix . 'siteid'] = $this->getSiteId();
			$list[$this->bprefix . 'ctime'] = $this->getTimeCreated();
			$list[$this->bprefix . 'mtime'] = $this->getTimeModified();
			$list[$this->bprefix . 'editor'] = $this->editor();
		}

		foreach( $this->bdata as $key => $value )
		{
			if( strpos( $key, '.' ) === false ) {
				$list[$key] = $value;
			}
		}

		return $list;
	}


	/**
	 * Tests if the date parameter represents an ISO format.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format or null
	 * @return string|null Clean date or null for no date
	 * @throws \Aimeos\MShop\Exception If the date is invalid
	 */
	protected function checkDateFormat( ?string $date ) : ?string
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9](( |T)[0-2][0-9]:[0-5][0-9](:[0-5][0-9])?)?$/';

		if( $date != null )
		{
			if( preg_match( $regex, (string) $date ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date, ISO format "YYYY-MM-DD hh:mm:ss" expected' ) );
			}

			if( strlen( $date ) === 16 ) {
				$date .= ':00';
			}

			return str_replace( 'T', ' ', (string) $date );
		}

		return null;
	}


	/**
	 * Tests if the date param represents an ISO format.
	 *
	 * @param string|null $date ISO date in YYYY-MM-DD format or null for no date
	 */
	protected function checkDateOnlyFormat( ?string $date ) : ?string
	{
		if( $date !== null && $date !== '' )
		{
			if( preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', (string) $date ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date, ISO format "YYYY-MM-DD" expected' ) );
			}

			return (string) $date;
		}

		return null;
	}


	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @param int $length Number of allowed characters
	 * @return string Item code
	 * @throws \Aimeos\MShop\Exception If the code is invalid
	 */
	protected function checkCode( string $code, int $length = 64 ) : string
	{
		if( strlen( $code ) > $length ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Code is too long' ) );
		}

		if( preg_match( '/[ \x{0000}\x{0009}\x{000A}\x{000C}\x{000D}\x{0085}]+/u', $code ) === 1 ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Code contains invalid characters: "%1$s"', $code ) );
		}

		return $code;
	}


	/**
	 * Tests if the country ID parameter represents an ISO country format.
	 *
	 * @param string|null $countryid Two letter ISO country format, e.g. DE
	 * @param bool $null True if null is allowed, false if not
	 * @return string|null Two letter ISO country ID or null for no country
	 * @throws \Aimeos\MShop\Exception If the country ID is invalid
	 */
	protected function checkCountryId( ?string $countryid, bool $null = true ) : ?string
	{
		if( $null === false && $countryid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO country code' ) );
		}

		if( $countryid != null )
		{
			if( preg_match( '/^[A-Za-z]{2}$/', $countryid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO country code' ) );
			}

			return strtoupper( $countryid );
		}

		return null;
	}


	/**
	 * Tests if the currency ID parameter represents an ISO currency format.
	 *
	 * @param string|null $currencyid Three letter ISO currency format, e.g. EUR
	 * @param bool $null True if null is allowed, false if not
	 * @return string|null Three letter ISO currency ID or null for no currency
	 * @throws \Aimeos\MShop\Exception If the currency ID is invalid
	 */
	protected function checkCurrencyId( ?string $currencyid, bool $null = true ) : ?string
	{
		if( $null === false && $currencyid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code' ) );
		}

		if( $currencyid != null )
		{
			if( preg_match( '/^[A-Za-z]{3}$/', $currencyid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO currency code' ) );
			}

			return strtoupper( $currencyid );
		}

		return null;
	}


	/**
	 * Tests if the language ID parameter represents an ISO language format.
	 *
	 * @param string|null $langid ISO language format, e.g. de or de_DE
	 * @param bool $null True if null is allowed, false if not
	 * @return string|null ISO language ID or null for no language
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	protected function checkLanguageId( ?string $langid, bool $null = true ) : ?string
	{
		if( $null === false && $langid == null ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO language code' ) );
		}

		if( $langid != null )
		{
			if( preg_match( '/^[a-zA-Z]{2}(_[a-zA-Z]{2})?$/', $langid ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid ISO language code' ) );
			}

			$parts = explode( '_', $langid );
			$parts[0] = strtolower( $parts[0] );

			if( isset( $parts[1] ) ) {
				$parts[1] = strtoupper( $parts[1] );
			}

			return implode( '_', $parts );
		}

		return null;
	}
}
