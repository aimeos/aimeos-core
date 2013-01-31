<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */

/**
 * Translation interface
 *
 * @package MW
 * @subpackage Translation
 */
interface MW_Translation_Interface
{
	/**
	 * Returns the translated string.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 * @throws @throws MW_Translation_Exception Throws exception on initialization of the translation
	 */
	public function dt( $domain, $string );

	/**
	 * Returns the translated string by the given plural and quantity.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to chose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number.
	 * @throws MW_Translation_Exception Throws exception on initialization of the translation
	 */
	public function dn( $domain, $singular, $plural, $number );


	/**
	 * Returns the current locale string.
	 *
	 * @return string ISO locale string
	 */
	public function getLocale();
}
