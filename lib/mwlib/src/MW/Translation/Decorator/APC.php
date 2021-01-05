<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation\Decorator;


/**
 * APC caching decorator for translation classes.
 *
 * @package MW
 * @subpackage Translation
 */
class APC
	extends \Aimeos\MW\Translation\Decorator\Base
	implements \Aimeos\MW\Translation\Decorator\Iface
{
	private $enable = false;
	private $prefix;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Translation\Iface $object Translation object or decorator
	 * @param string $prefix Prefix for keys to distinguish several instances
	 */
	public function __construct( \Aimeos\MW\Translation\Iface $object, string $prefix = '' )
	{
		parent::__construct( $object );

		if( function_exists( 'apcu_store' ) === true )
		{
			$this->enable = true;
			$this->prefix = $prefix;
		}
	}


	/**
	 * Returns the translated string.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 */
	public function dt( string $domain, string $string ) : string
	{
		if( $this->enable === false ) {
			return parent::dt( $domain, $string );
		}

		$key = $this->prefix . $domain . '|' . $this->getLocale() . '|' . $string;

		// regular cache
		$success = false;
		$value = apcu_fetch( $key, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		$value = parent::dt( $domain, $string );

		apcu_store( $key, $value );

		return $value;
	}


	/**
	 * Returns the translated string by the given plural and quantity.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param int $number Quantity to chose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number.
	 */
	public function dn( string $domain, string $singular, string $plural, int $number ) : string
	{
		if( $this->enable === false ) {
			return parent::dn( $domain, $singular, $plural, $number );
		}

		$locale = $this->getLocale();
		$index = $this->getPluralIndex( $number, $locale );
		$key = $this->prefix . $domain . '|' . $locale . '|' . $singular . '|' . $index;

		// regular cache
		$success = false;
		$value = apcu_fetch( $key, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		$value = parent::dn( $domain, $singular, $plural, $number );

		apcu_store( $key, $value );

		return $value;
	}
}
