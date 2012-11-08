<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 * @version $Id: None.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Translation without an implementation; dummy class
 *
 * @package MW
 * @subpackage Translation
 */
class MW_Translation_None
	extends MW_Translation_Abstract
	implements MW_Translation_Interface
{
	private $_locale;


	/**
	 * Initializes the translation object.
	 *
	 * @param string $locale Locale string, e.g. en or en_GB
	 */
	public function __construct( $locale )
	{
		$this->_locale = (string) $locale;
	}


	/**
	 * Returns the given string for the given domain.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 * @throws MW_Translation_Exception Throws exception on initialization of the translation
	 */
	public function dt( $domain, $string )
	{
		return (string) $string;
	}


	/**
	 * Returns the plural string by given number.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to choose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number
	 * @throws MW_Translation_Exception If the initialization of the translation
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		if( $this->_getPluralIndex( $number, $this->_locale ) > 0 ) {
			return (string) $plural;
		}

		return (string) $singular;
	}

}
