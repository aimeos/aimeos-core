<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 */


/**
 * Memory caching decorator for translation classes.
 *
 * @package MW
 * @subpackage Translation
 */
class MW_Translation_Decorator_Memory
	extends MW_Translation_Decorator_Abstract
	implements MW_Translation_Decorator_Interface
{
	private $translations;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Interface $object Translation object or decorator
	 * @param array $translations Associative list of domains and singular
	 * 	strings as key and list of translation number and translations as value:
	 * 	array( <domain> => array( <singular> => array( <index> => <translations> ) ) )
	 */
	public function __construct( MW_Translation_Interface $object, array $translations = array() )
	{
		parent::__construct( $object );
		$this->translations = $translations;
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
		if( isset( $this->translations[$domain][$string][0] ) ) {
			return $this->translations[$domain][$string][0];
		}

		return parent::dt( $domain, $string );
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
		$index = $this->getPluralIndex( $number, $this->getLocale() );

		if( isset( $this->translations[$domain][$singular][$index] ) ) {
			return $this->translations[$domain][$singular][$index];
		}

		return parent::dn( $domain, $singular, $plural, $number );
	}
}
