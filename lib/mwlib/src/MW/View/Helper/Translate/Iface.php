<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Translate;


/**
 * View helper class for translating strings.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the translated string or the original one if no translation is available.
	 *
	 * @param string $domain Translation domain from core or an extension
	 * @param string $singular Singular form of the text to translate
	 * @param string $plural Plural form of the text, used if $number is greater than one
	 * @param integer $number Amount of things relevant for the plural form
	 * @return string Translated string
	 */
	public function transform( $domain, $singular, $plural = '', $number = 1 );
}