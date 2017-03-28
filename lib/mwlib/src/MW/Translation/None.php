<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation;


/**
 * Translation without an implementation; dummy class
 *
 * @package MW
 * @subpackage Translation
 */
class None
	extends \Aimeos\MW\Translation\Base
	implements \Aimeos\MW\Translation\Iface
{
	/**
	 * Returns the given string for the given domain.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 * @throws \Aimeos\MW\Translation\Exception Throws exception on initialization of the translation
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
	 * @throws \Aimeos\MW\Translation\Exception If the initialization of the translation
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		if( $this->getPluralIndex( $number, $this->getLocale() ) > 0 ) {
			return (string) $plural;
		}

		return (string) $singular;
	}


	/**
	 * Returns all locale string of the given domain.
	 *
	 * @param string $domain Translation domain
	 * @return array Associative list with original string as key and translation
	 * 	as value or an associative list with index => translation as value if
	 * 	plural forms are available
	 */
	public function getAll( $domain )
	{
		return [];
	}

}
