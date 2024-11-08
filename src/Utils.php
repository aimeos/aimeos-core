<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023-2024
 */


namespace Aimeos;


/**
 * Utility methods
 */
class Utils
{
	/**
	 * Creates a new object instance
	 *
	 * @param string $class Name of the class
	 * @param array $args Constructor arguments
	 * @param string|null $iface Name of the interface the object must implement
	 * @return object New object instance
	 * @throws \LogicException If the class isn't found or doesn't implement the interface
	 * @todo 2025.01 Allow list of interfaces to check for common and specific interfaces
	 */
	public static function create( string $class, array $args, ?string $iface = null ) : object
	{
		if( class_exists( $class ) === false ) {
			throw new \LogicException( sprintf( 'Class "%1$s" not found', $class ), 400 );
		}

		$object = new $class( ...$args );

		if( $iface && !( $object instanceof $iface ) ) {
			throw new \LogicException( sprintf( 'Class "%1$s" does not implement "%2$s"', $class, $iface ), 400 );
		}

		return $object;
	}


	/**
	 * Tests if the code is valid.
	 *
	 * @param string $code New code for an item
	 * @param int $length Number of allowed characters
	 * @return string Item code
	 * @throws \RuntimeException If the code is invalid
	 */
	public static function code( string $code, int $length = 64 ) : string
	{
		if( strlen( $code ) > $length ) {
			throw new \RuntimeException( sprintf( 'Code is too long' ) );
		}

		if( preg_match( '/[ \x{0000}\x{0009}\x{000A}\x{000C}\x{000D}\x{0085}]+/u', $code ) === 1 ) {
			throw new \RuntimeException( sprintf( 'Code contains invalid characters: "%1$s"', $code ) );
		}

		return $code;
	}


	/**
	 * Tests if the country ID parameter represents an ISO country format.
	 *
	 * @param string|null $countryid Two letter ISO country format, e.g. DE
	 * @param bool $null True if null is allowed, false if not
	 * @return string|null Two letter ISO country ID or null for no country
	 * @throws \RuntimeException If the country ID is invalid
	 */
	public static function country( ?string $countryid, bool $null = true ) : ?string
	{
		if( !$null && !$countryid ) {
			throw new \RuntimeException( sprintf( 'Invalid ISO country code' ) );
		}

		if( $countryid )
		{
			if( preg_match( '/^[A-Za-z]{2}$/', $countryid ) !== 1 ) {
				throw new \RuntimeException( sprintf( 'Invalid ISO country code' ) );
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
	 * @throws \RuntimeException If the currency ID is invalid
	 */
	public static function currency( ?string $currencyid, bool $null = true ) : ?string
	{
		if( !$null && !$currencyid ) {
			throw new \RuntimeException( sprintf( 'Invalid ISO currency code' ) );
		}

		if( $currencyid )
		{
			if( preg_match( '/^[A-Za-z]{3}$/', $currencyid ) !== 1 ) {
				throw new \RuntimeException( sprintf( 'Invalid ISO currency code' ) );
			}

			return strtoupper( $currencyid );
		}

		return null;
	}


	/**
	 * Tests if the date param represents an ISO format.
	 *
	 * @param string|null $date ISO date in YYYY-MM-DD format or null for no date
	 */
	public static function date( ?string $date ) : ?string
	{
		if( $date )
		{
			if( preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', (string) $date ) !== 1 ) {
				throw new \RuntimeException( sprintf( 'Invalid characters in date, ISO format "YYYY-MM-DD" expected' ) );
			}

			return (string) $date;
		}

		return null;
	}


	/**
	 * Tests if the date parameter represents an ISO format.
	 *
	 * @param string|null $date ISO date in yyyy-mm-dd HH:ii:ss format or null
	 * @return string|null Clean date or null for no date
	 * @throws \RuntimeException If the date is invalid
	 */
	public static function datetime( ?string $date ) : ?string
	{
		$regex = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9](( |T)[0-2][0-9]:[0-5][0-9](:[0-5][0-9])?)?$/';

		if( $date )
		{
			if( preg_match( $regex, (string) $date ) !== 1 ) {
				throw new \RuntimeException( sprintf( 'Invalid characters in date, ISO format "YYYY-MM-DD hh:mm:ss" expected' ) );
			}

			if( strlen( $date ) === 16 ) {
				$date .= ':00';
			}

			return str_replace( 'T', ' ', (string) $date );
		}

		return null;
	}


	/**
	 * Checks if the object implements the given interface
	 *
	 * @param object $object Object to check
	 * @param string $iface Name of the interface the object must implement
	 * @return object Same object as passed in
	 * @throws \LogicException If the object doesn't implement the interface
	 * @todo 2025.01 Allow list of interfaces to check for common and specific interfaces
	 */
	public static function implements( object $object, string $iface ) : object
	{
		if( !( $object instanceof $iface ) ) {
			throw new \LogicException( sprintf( 'Class "%1$s" does not implement "%2$s"', get_class( $object ), $iface ), 400 );
		}

		return $object;
	}


	/**
	 * Tests if the language ID parameter represents an ISO language format.
	 *
	 * @param string|null $langid ISO language format, e.g. de or de_DE
	 * @param bool $null True if null is allowed, false if not
	 * @return string|null ISO language ID or null for no language
	 * @throws \RuntimeException If the language ID is invalid
	 */
	public static function language( ?string $langid, bool $null = true ) : ?string
	{
		if( !$null && !$langid ) {
			throw new \RuntimeException( sprintf( 'Invalid ISO language code' ) );
		}

		if( $langid )
		{
			if( preg_match( '/^[a-zA-Z]{2}(_[a-zA-Z]{2})?$/', $langid ) !== 1 ) {
				throw new \RuntimeException( sprintf( 'Invalid ISO language code' ) );
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
