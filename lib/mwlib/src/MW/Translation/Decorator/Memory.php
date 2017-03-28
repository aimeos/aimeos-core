<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Translation
 */


namespace Aimeos\MW\Translation\Decorator;


/**
 * Memory caching decorator for translation classes.
 *
 * @package MW
 * @subpackage Translation
 */
class Memory
	extends \Aimeos\MW\Translation\Decorator\Base
	implements \Aimeos\MW\Translation\Decorator\Iface
{
	private $translations;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Translation\Iface $object Translation object or decorator
	 * @param array $translations Associative list of domains and singular
	 * 	strings as key and list of translation number and translations as value:
	 * 	array( <domain> => array( <singular> => array( <index> => <translations> ) ) )
	 */
	public function __construct( \Aimeos\MW\Translation\Iface $object, array $translations = [] )
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
