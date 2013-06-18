<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 * @version $Id: Abstract.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Base class for all translator decorators.
 *
 * @package MW
 * @subpackage Translation
 */
abstract class MW_Translation_Decorator_Abstract
	extends MW_Translation_Abstract
	implements MW_Translation_Decorator_Interface
{
	private $_object;
	private $_config;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Interface $object Translation object or decorator
	 * @param MW_Config_Interface $config Configuration object
	 */
	public function __construct( MW_Translation_Interface $object, MW_Config_Interface $config )
	{
		$this->_object = $object;
		$this->_config = $config;
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
		return $this->_object->dt( $domain, $string );
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
		return $this->_object->dn( $domain, $singular, $plural, $number );
	}


	/**
	 * Returns the current locale string.
	 *
	 * @return string ISO locale string
	 */
	public function getLocale()
	{
		return $this->_object->getLocale();
	}


	/**
	 * Returns the config object.
	 *
	 * @return MW_Config_Interface Config object
	 */
	protected function _getConfig()
	{
		return $this->_config;
	}


	/**
	 * Returns the wrapped translation object.
	 *
	 * @return MW_Translation_Interface Translation object
	 */
	protected function _getObject()
	{
		return $this->_object;
	}
}
