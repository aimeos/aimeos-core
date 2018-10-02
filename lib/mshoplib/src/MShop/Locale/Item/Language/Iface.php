<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Language;


/**
 * Common interface for all language items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode();

	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $key two letter ISO language code
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setCode( $key );

	/**
	 * Returns the label property of the language.
	 *
	 * @return string Label or symbol of the language
	 */
	public function getLabel();

	/**
	 * Sets the label property of the language.
	 *
	 * @param string $label Label or symbol of the language
	 * @return \Aimeos\MShop\Locale\Item\Language\Iface Locale language item for chaining method calls
	 */
	public function setLabel( $label );
}
