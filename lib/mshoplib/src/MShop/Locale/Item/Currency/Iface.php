<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Currency;


/**
 * Common interface for all currency items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode() : string;

	/**
	 * Sets the code of the currency.
	 *
	 * @param string $key Code of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setCode( string $key ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel() : string;

	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Locale\Item\Currency\Iface;
}
