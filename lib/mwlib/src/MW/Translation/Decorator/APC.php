<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 * @version $Id: APC.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * APC caching decorator for translation classes.
 *
 * @package MW
 * @subpackage Translation
 */
class MW_Translation_Decorator_APC
	extends MW_Translation_Decorator_Abstract
	implements MW_Translation_Decorator_Interface
{
	private $_prefix;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Interface $object Translation object or decorator
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function __construct( MW_Translation_Interface $object, MW_Config_Interface $config )
	{
		if( function_exists( 'apc_store' ) === false ) {
			throw new MW_Translation_Exception( 'APC not available' );
		}

		parent::__construct( $object, $config );

		$this->_prefix = $config->get( 'resource/translation/decorator/apc/prefix', 'translate:' );
	}


	/**
	 * Returns the translated string.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 */
	public function dt( $domain, $string )
	{
		$key = $this->_prefix . $domain . '|' . $this->getLocale() . '|' . $string;

		// regular cache
		$success = false;
		$value = apc_fetch( $key, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		$value = parent::dt( $domain, $string );

		apc_store( $key, $value );

		return $value;
	}


	/**
	 * Returns the translated string by the given plural and quantity.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to chose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number.
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		$locale = $this->getLocale();
		$index = $this->_getPluralIndex( $number, $locale );
		$key = $this->_prefix . $domain . '|' . $locale . '|' . $singular . '|' . $index;

		// regular cache
		$success = false;
		$value = apc_fetch( $key, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		$value = parent::dn( $domain, $singular, $plural, $number );

		apc_store( $key, $value );

		return $value;
	}
}
